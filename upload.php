<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/github_import.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['creator', 'admin'])) {
    header('Location: login.php?redirect=upload.php');
    exit;
}

$message     = '';
$messageType = '';
$activeTab   = 'import';

$title       = '';
$description = '';
$author      = '';
$category    = '';
$ageGroup    = '';
$githubLink  = '';
$repoUrl     = '';
$importMeta  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode      = $_POST['mode'] ?? 'manual';
    $activeTab = $mode;

    if ($mode === 'import') {
        $repoUrl = trim($_POST['repo_url'] ?? '');
        if (empty($repoUrl)) {
            $message     = 'Ingresa la URL del repositorio de GitHub.';
            $messageType = 'error';
        } else {
            $result      = importFromGithub($repoUrl, $pdo);
            $message     = $result['message'];
            $messageType = $result['ok'] ? 'success' : 'error';
            if ($result['ok']) {
                $importMeta = $result['meta'];
                $repoUrl    = '';
            }
        }
    } else {
        $title       = sanitizeInput($_POST['title']       ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $author      = sanitizeInput($_POST['author']      ?? '');
        $category    = sanitizeInput($_POST['category']    ?? '');
        $ageGroup    = sanitizeInput($_POST['age_group']   ?? '');
        $githubLink  = sanitizeInput($_POST['github_link'] ?? '');

        if (empty($title) || empty($description) || empty($author) || empty($category) || empty($ageGroup) || empty($githubLink)) {
            $message     = 'Todos los campos son obligatorios.';
            $messageType = 'error';
        } elseif (!preg_match('/^https:\/\/[a-zA-Z0-9\-_]+\.github\.io\/[a-zA-Z0-9\-_\/]*$/', $githubLink)) {
            $message     = 'Por favor ingresa un enlace válido de GitHub Pages (debe contener github.io).';
            $messageType = 'error';
        } else {
            $folderName = sanitizeFilename($title . '-' . time());
            $stmt = $pdo->prepare(
                'INSERT INTO games (title, description, author, folder_name, category, age_group, github_link)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            if ($stmt->execute([$title, $description, $author, $folderName, $category, $ageGroup, $githubLink])) {
                $message     = 'Juego registrado exitosamente. Está pendiente de aprobación.';
                $messageType = 'success';
                $title = $description = $author = $category = $ageGroup = $githubLink = '';
            } else {
                $message     = 'Error al guardar en la base de datos.';
                $messageType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Juego — ZELIA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<main class="upload-page">
    <div class="container">

        <div class="page-header">
            <div class="section-badge">PUBLICAR</div>
            <h1 class="page-title">Subir un juego</h1>
            <p class="page-subtitle">Comparte tu juego con la comunidad educativa</p>
        </div>

        <div class="upload-card">
            <div class="upload-card-header">
                <div class="upload-tabs">
                    <button class="upload-tab <?= $activeTab === 'import' ? 'active' : '' ?>" onclick="switchTab('import')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                        Importar Repo
                    </button>
                    <button class="upload-tab <?= $activeTab === 'manual' ? 'active' : '' ?>" onclick="switchTab('manual')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Formulario
                    </button>
                </div>
            </div>

            <div class="upload-card-body">

                <?php if ($message): ?>
                    <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <?php if ($importMeta): ?>
                    <div class="import-summary">
                        <div class="import-summary-title">✓ Juego importado</div>
                        <ul>
                            <li><strong>Título:</strong> <?= htmlspecialchars($importMeta['title'] ?? '') ?></li>
                            <li><strong>Autor:</strong>  <?= htmlspecialchars($importMeta['author'] ?? '') ?></li>
                            <li><strong>Carpeta:</strong> games/<?= htmlspecialchars($importMeta['folder'] ?? '') ?></li>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- TAB: Importar desde GitHub -->
                <div class="upload-tab-panel <?= $activeTab === 'import' ? 'active' : '' ?>" id="panel-import">
                    <div class="upload-kicker">
                        Pega la URL de un repositorio público de GitHub. El sistema descargará los archivos y extraerá la metadata del <code>info.json</code> si existe.
                    </div>

                    <div class="upload-checklist">
                        <div class="upload-checklist-title">El repositorio debe tener:</div>
                        <ul>
                            <li>Un archivo <code>index.html</code> en la raíz</li>
                            <li>Repositorio público (sin autenticación)</li>
                            <li>Opcionalmente un <code>info.json</code> con título, descripción, categoría y edad</li>
                        </ul>
                    </div>

                    <form method="POST">
                        <input type="hidden" name="mode" value="import">
                        <div class="form-group">
                            <label class="form-label" for="repo_url">URL del repositorio GitHub</label>
                            <input
                                type="url"
                                id="repo_url"
                                name="repo_url"
                                class="form-input"
                                placeholder="https://github.com/usuario/mi-juego"
                                value="<?= htmlspecialchars($repoUrl) ?>"
                                required
                            >
                        </div>
                        <div class="upload-actions">
                            <button type="submit" class="btn btn-primary">Importar juego</button>
                        </div>
                    </form>
                </div>

                <!-- TAB: Formulario manual -->
                <div class="upload-tab-panel <?= $activeTab === 'manual' ? 'active' : '' ?>" id="panel-manual">
                    <div class="upload-kicker">
                        Ingresa los datos del juego manualmente. El juego debe estar publicado en GitHub Pages.
                    </div>

                    <form method="POST">
                        <input type="hidden" name="mode" value="manual">

                        <div class="form-group">
                            <label class="form-label" for="title">Título del juego</label>
                            <input type="text" id="title" name="title" class="form-input"
                                placeholder="Mi juego educativo"
                                value="<?= htmlspecialchars($title) ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="description">Descripción</label>
                            <textarea id="description" name="description" class="form-textarea"
                                placeholder="Breve descripción del juego y su objetivo educativo…"
                                required><?= htmlspecialchars($description) ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="author">Autor</label>
                                <input type="text" id="author" name="author" class="form-input"
                                    placeholder="Tu nombre"
                                    value="<?= htmlspecialchars($author) ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="category">Categoría</label>
                                <input type="text" id="category" name="category" class="form-input"
                                    placeholder="Ej: Matemáticas"
                                    value="<?= htmlspecialchars($category) ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="age_group">Grupo de edad</label>
                                <select id="age_group" name="age_group" class="form-select" required>
                                    <option value="">Seleccionar…</option>
                                    <?php
                                    $ages = ['6-8 años','9-11 años','12-14 años','15+ años','Todas las edades'];
                                    foreach ($ages as $a):
                                    ?>
                                    <option value="<?= htmlspecialchars($a) ?>" <?= $ageGroup === $a ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($a) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="github_link">Enlace GitHub Pages</label>
                                <input type="url" id="github_link" name="github_link" class="form-input"
                                    placeholder="https://usuario.github.io/juego"
                                    value="<?= htmlspecialchars($githubLink) ?>" required>
                                <span class="form-hint">Debe contener github.io</span>
                            </div>
                        </div>

                        <div class="upload-actions">
                            <button type="submit" class="btn btn-primary">Publicar juego</button>
                            <a href="index.php" class="btn btn-ghost">Cancelar</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script>
function switchTab(tab) {
    document.querySelectorAll('.upload-tab').forEach(function(btn, i) {
        btn.classList.toggle('active', (i === 0 && tab === 'import') || (i === 1 && tab === 'manual'));
    });
    document.querySelectorAll('.upload-tab-panel').forEach(function(panel) {
        panel.classList.toggle('active', panel.id === 'panel-' + tab);
    });
}
</script>
</body>
</html>
