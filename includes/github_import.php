<?php
/**
 * Importa un juego desde un repositorio público de GitHub.
 *
 * Descarga el ZIP, lo extrae de forma segura, lee info.json para los
 * metadatos, copia los archivos a games/{folder}/ e inserta en la BD.
 *
 * @param  string $repoUrl  URL del repo (https://github.com/owner/repo o .git)
 * @param  PDO    $pdo
 * @return array  ['ok' => bool, 'message' => string, 'game_id' => int|null, 'meta' => array|null]
 */
function importFromGithub(string $repoUrl, PDO $pdo): array
{
    // ── 1. Validar y normalizar la URL ────────────────────────────────────
    $repoUrl = trim($repoUrl);
    $repoUrl = preg_replace('/\.git$/i', '', $repoUrl);   // quitar .git si viene

    if (!preg_match(
        '#^https://github\.com/([a-zA-Z0-9][a-zA-Z0-9\-]{0,38})/([a-zA-Z0-9\-\._]{1,100})$#',
        $repoUrl,
        $m
    )) {
        return ['ok' => false, 'message' => 'URL inválida. Formato esperado: https://github.com/usuario/repositorio'];
    }

    $owner      = $m[1];
    $repo       = $m[2];
    $folderName = ghSanitizeFolder($repo);

    // ── 2. Verificar que no exista ya ese folder_name en la BD ────────────
    $check = $pdo->prepare('SELECT id FROM games WHERE folder_name = ?');
    $check->execute([$folderName]);
    if ($check->fetch()) {
        return ['ok' => false, 'message' => "Ya existe un juego con el identificador <strong>$folderName</strong>."];
    }

    // ── 3. Descargar el ZIP desde la API de GitHub ────────────────────────
    if (!function_exists('curl_init')) {
        return ['ok' => false, 'message' => 'cURL no está disponible en este servidor.'];
    }
    if (!class_exists('ZipArchive')) {
        return ['ok' => false, 'message' => 'La extensión ZipArchive no está disponible en este servidor.'];
    }

    $zipUrl = "https://api.github.com/repos/{$owner}/{$repo}/zipball/HEAD";
    $tmpZip = tempnam(sys_get_temp_dir(), 'ghimp_') . '.zip';

    $ch = curl_init($zipUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_TIMEOUT        => 40,
        CURLOPT_USERAGENT      => 'game-php-importer/1.0',
        CURLOPT_HTTPHEADER     => ['Accept: application/vnd.github+json'],
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $zipData  = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($zipData === false || $httpCode !== 200) {
        $hint = $httpCode === 404 ? ' (repositorio no encontrado o privado)' : " (HTTP $httpCode)";
        return ['ok' => false, 'message' => "No se pudo descargar el repositorio$hint. $curlErr"];
    }

    if (strlen($zipData) > 50 * 1024 * 1024) {
        return ['ok' => false, 'message' => 'El repositorio supera el límite de 50 MB.'];
    }

    file_put_contents($tmpZip, $zipData);
    unset($zipData);

    // ── 4. Extraer el ZIP de forma segura ─────────────────────────────────
    $tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ghimp_' . bin2hex(random_bytes(8));
    mkdir($tmpDir, 0700, true);

    $zip = new ZipArchive();
    if ($zip->open($tmpZip) !== true) {
        unlink($tmpZip);
        ghRemoveDir($tmpDir);
        return ['ok' => false, 'message' => 'No se pudo abrir el archivo ZIP.'];
    }

    // Los ZIPs de GitHub tienen un prefijo raíz: "owner-repo-sha/"
    $rootPrefix = '';
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $n = $zip->getNameIndex($i);
        if (substr($n, -1) === '/' && substr_count(rtrim($n, '/'), '/') === 0) {
            $rootPrefix = $n;
            break;
        }
    }

    $allowedExts = [
        'html', 'htm', 'css', 'js', 'json', 'txt', 'md',
        'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'ico',
        'mp3', 'wav', 'ogg', 'woff', 'woff2', 'ttf',
    ];

    $extracted = 0;
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);

        // Quitar prefijo raíz
        $rel = ($rootPrefix !== '' && strpos($name, $rootPrefix) === 0)
            ? substr($name, strlen($rootPrefix))
            : $name;

        $rel = ltrim(str_replace('\\', '/', $rel), '/');
        if ($rel === '') continue;

        // Bloquear path traversal
        if (strpos($rel, '..') !== false || preg_match('#(^|/)\.#', $rel)) {
            continue;
        }

        if (substr($name, -1) === '/') {
            // Entrada de directorio
            $d = $tmpDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
            if (!is_dir($d)) {
                mkdir($d, 0755, true);
            }
            continue;
        }

        $ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts, true)) {
            continue;
        }

        $dest    = $tmpDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
        $destDir = dirname($dest);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $content = $zip->getFromIndex($i);
        if ($content !== false) {
            file_put_contents($dest, $content);
            $extracted++;
        }
    }
    $zip->close();
    unlink($tmpZip);

    if ($extracted === 0) {
        ghRemoveDir($tmpDir);
        return ['ok' => false, 'message' => 'El repositorio no contiene archivos válidos (html, css, js...).'];
    }

    if (!file_exists($tmpDir . DIRECTORY_SEPARATOR . 'index.html')) {
        ghRemoveDir($tmpDir);
        return ['ok' => false, 'message' => 'El repositorio no tiene un <strong>index.html</strong> en la raíz.'];
    }

    // ── 5. Leer info.json ─────────────────────────────────────────────────
    $meta = ghReadInfoJson($tmpDir, $repo, $owner);

    // ── 6. Copiar archivos a games/{folder_name}/ ─────────────────────────
    $gamesBase = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'games';
    $destGame  = $gamesBase . DIRECTORY_SEPARATOR . $folderName;

    if (is_dir($destGame)) {
        ghRemoveDir($destGame);
    }
    mkdir($destGame, 0755, true);
    ghCopyDir($tmpDir, $destGame);
    ghRemoveDir($tmpDir);

    // ── 7. Insertar en la base de datos ───────────────────────────────────
    $approved = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 1 : 0;

    $stmt = $pdo->prepare(
        'INSERT INTO games (title, description, author, folder_name, category, age_group, github_link, approved)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $meta['title'],
        $meta['description'],
        $meta['author'],
        $folderName,
        $meta['category'],
        $meta['age_group'],
        $repoUrl,
        $approved,
    ]);

    $gameId    = (int) $pdo->lastInsertId();
    $approveMsg = $approved
        ? ' Está disponible de inmediato.'
        : ' Queda pendiente de aprobación.';

    return [
        'ok'      => true,
        'message' => "Juego <strong>{$meta['title']}</strong> importado correctamente.{$approveMsg}",
        'game_id' => $gameId,
        'folder'  => $folderName,
        'meta'    => $meta,
    ];
}

// ── Helpers ───────────────────────────────────────────────────────────────────

/** Convierte el nombre del repo en un folder_name seguro. */
function ghSanitizeFolder(string $name): string
{
    $name = strtolower(trim($name));
    $name = preg_replace('/[^a-z0-9\-]/', '-', $name);
    $name = preg_replace('/-+/', '-', $name);
    return trim($name, '-');
}

/**
 * Lee info.json del directorio extraído.
 * Soporta estructura plana { "titulo": ... } y anidada { "key": { "titulo": ... } }.
 */
function ghReadInfoJson(string $dir, string $repo, string $owner): array
{
    $defaults = [
        'title'       => ucwords(str_replace(['-', '_'], ' ', $repo)),
        'description' => 'Juego importado desde GitHub.',
        'author'      => $owner,
        'category'    => 'Lógica',
        'age_group'   => '13-16 años',
    ];

    $jsonPath = $dir . DIRECTORY_SEPARATOR . 'info.json';
    if (!file_exists($jsonPath)) {
        return $defaults;
    }

    $raw = json_decode(file_get_contents($jsonPath), true);
    if (!is_array($raw)) {
        return $defaults;
    }

    // Si no tiene claves de metadatos directamente, tomar el primer valor (estructura anidada)
    $keys = ['titulo', 'title', 'nombre', 'descripcion', 'description', 'autor', 'author'];
    $hasDirectKey = array_intersect($keys, array_keys($raw)) !== [];
    if (!$hasDirectKey) {
        $first = reset($raw);
        if (is_array($first)) {
            $raw = $first;
        }
    }

    $s = fn($v) => htmlspecialchars(strip_tags(trim((string)$v)), ENT_QUOTES, 'UTF-8');

    return [
        'title'       => $s($raw['titulo']      ?? $raw['title']     ?? $raw['nombre']   ?? $defaults['title']),
        'description' => $s($raw['descripcion'] ?? $raw['description']                   ?? $defaults['description']),
        'author'      => $s($raw['autor']        ?? $raw['author']                        ?? $defaults['author']),
        'category'    => $s($raw['categoria']   ?? $raw['category']                      ?? $defaults['category']),
        'age_group'   => $s($raw['edad']         ?? $raw['age_group'] ?? $raw['ageGroup'] ?? $defaults['age_group']),
    ];
}

/** Copia recursivamente un directorio. */
function ghCopyDir(string $src, string $dst): void
{
    $dir = opendir($src);
    while (($file = readdir($dir)) !== false) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $s = $src . DIRECTORY_SEPARATOR . $file;
        $d = $dst . DIRECTORY_SEPARATOR . $file;
        if (is_dir($s)) {
            mkdir($d, 0755, true);
            ghCopyDir($s, $d);
        } else {
            copy($s, $d);
        }
    }
    closedir($dir);
}

/** Elimina un directorio completo de forma recursiva. */
function ghRemoveDir(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $item) {
        $item->isDir() ? rmdir($item->getRealPath()) : unlink($item->getRealPath());
    }
    rmdir($dir);
}
