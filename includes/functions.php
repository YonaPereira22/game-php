<?php
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function sanitizeFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9\-_.]/', '', $filename);
    return strtolower($filename);
}

function getAvailableGameIcons(string $iconDir = 'images/game-icons'): array {
    $dir = __DIR__ . '/../' . ltrim($iconDir, '/');
    if (!is_dir($dir)) {
        return [];
    }

    $allowedExtensions = ['png', 'jpg', 'jpeg', 'svg', 'webp'];
    $icons = [];
    $entries = scandir($dir);

    if ($entries === false) {
        return [];
    }

    foreach ($entries as $entry) {
        if ($entry === '.' || $entry === '..' || $entry === 'README.md') {
            continue;
        }

        $path = $dir . '/' . $entry;
        if (is_file($path)) {
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (in_array($extension, $allowedExtensions, true)) {
                $icons[] = $entry;
            }
        }
    }

    sort($icons, SORT_STRING);
    return $icons;
}

function renderMarkdown(string $markdown): string {
    $text = htmlspecialchars($markdown, ENT_QUOTES, 'UTF-8');
    $text = preg_replace_callback('/\[(.*?)\]\((.*?)\)/', function ($matches) {
        $url = filter_var($matches[2], FILTER_SANITIZE_URL);
        return '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">' . $matches[1] . '</a>';
    }, $text);
    $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);
    $text = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $text);
    $text = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $text);
    $text = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $text);
    $text = preg_replace('/^- (.+)$/m', '<li>$1</li>', $text);
    if (strpos($text, '<li>') !== false) {
        $text = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $text);
    }
    return nl2br($text);
}

function validateGameFolder($folderPath) {
    if (!file_exists($folderPath . '/index.html')) {
        return false;
    }
    
    $indexContent = file_get_contents($folderPath . '/index.html');
    if (strpos($indexContent, '<script') !== false && 
        (strpos($indexContent, 'eval(') !== false || 
         strpos($indexContent, 'document.write') !== false)) {
        return false;
    }
    
    return true;
}

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function updateGameRating($gameId, $pdo) {
    $stmt = $pdo->prepare("
        UPDATE games 
        SET total_votes = (SELECT COUNT(*) FROM votes WHERE game_id = ?),
            average_rating = (SELECT AVG(rating) FROM votes WHERE game_id = ?)
        WHERE id = ?
    ");
    $stmt->execute([$gameId, $gameId, $gameId]);
}

// ================== FUNCIONES DE AUTENTICACIÓN ==================

/**
 * Verifica si el usuario está logueado
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirige a login si no está logueado
 * @param string $redirect - URL a redirigir después del login
 */
function requireLogin($redirect = null) {
    if (!isLoggedIn()) {
        $url = 'login.php';
        if ($redirect) {
            $url .= '?redirect=' . urlencode($redirect);
        }
        header('Location: ' . $url);
        exit;
    }
}

/**
 * Redirige a login si no es admin
 */
function requireAdmin() {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') {
        header('Location: index.php');
        exit;
    }
}

/**
 * Redirige a login si no es creador
 */
function requireCreator() {
    requireLogin();
    if (!in_array($_SESSION['role'], ['creator', 'admin'])) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Obtiene la información del usuario actual
 * @param PDO $pdo
 * @return array|null
 */
function getCurrentUser($pdo) {
    if (!isLoggedIn()) {
        return null;
    }
    
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Obtiene el nombre del usuario actual
 * @return string|null
 */
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

/**
 * Obtiene el rol del usuario actual
 * @return string|null
 */
function getCurrentRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Verifica si el usuario actual es admin
 * @return bool
 */
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

/**
 * Verifica si el usuario actual es creador
 * @return bool
 */
function isCreator() {
    return isLoggedIn() && in_array($_SESSION['role'], ['creator', 'admin']);
}

/**
 * Verifica si el usuario está usando Google OAuth
 * @return bool
 */
function isGoogleLogin() {
    return isset($_SESSION['google_picture']) && !empty($_SESSION['google_picture']);
}

/**
 * Obtiene la foto de perfil del usuario actual
 * @return string|null
 */
function getUserProfilePicture() {
    return $_SESSION['google_picture'] ?? null;
}
