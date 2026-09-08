<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/github_import.php';

// Controlar tipos de subida: prioridad a la configuración privada `$config['app']['upload_types']`,
// si no existe, caer a la variable de entorno `UPLOAD_TYPES`. Valores posibles aceptados:
// 'repo'|'repositorio' (importar desde repo), 'sitio'|'site' (formulario/manual), 'ambos'|'both'.
// Por defecto: 'ambos'.
if (!isset($config)) {
    $bootstrapPath = __DIR__ . '/config/bootstrap.php';
    if (is_readable($bootstrapPath)) {
        require_once $bootstrapPath;
    }
}

$upload_types = $config['app']['upload_types'] ?? $config['upload_types'] ?? getenv('UPLOAD_TYPES') ?: 'ambos';
$upload_types = strtolower(trim((string)$upload_types));

// Normalizar sinónimos
if (in_array($upload_types, ['both', 'botho'], true)) {
    $upload_types = 'ambos';
}
if (in_array($upload_types, ['site', 'sitio'], true)) {
    $upload_types = 'sitio';
}
if (in_array($upload_types, ['repo', 'repositorio'], true)) {
    $upload_types = 'repo';
}

if (!in_array($upload_types, ['repo', 'sitio', 'ambos'], true)) {
    $upload_types = 'ambos';
}

$allow_repo = $upload_types === 'repo' || $upload_types === 'ambos';
$allow_site = $upload_types === 'sitio' || $upload_types === 'ambos';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['creator', 'admin'])) {
    header('Location: login.php?redirect=upload.php');
    exit;
}

$message     = '';
$messageType = '';
$activeTab   = $allow_repo ? 'import' : ($allow_site ? 'manual' : 'import');

$availableIcons = getAvailableGameIcons();
$selectedIcon   = '';

$title       = '';
$description = '';
$author      = '';
$category    = '';
$ageGroup    = '';
$githubLink  = '';
$readme      = '';
$repoUrl     = '';
$importMeta  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode      = $_POST['mode'] ?? 'manual';
    $activeTab = $mode;

    // Evitar modos no permitidos por la configuración de entorno
    if ($mode === 'import' && !$allow_repo) {
        $message     = 'La importación desde repositorio no está permitida en este entorno.';
        $messageType = 'error';
    } elseif ($mode === 'manual' && !$allow_site) {
        $message     = 'La publicación manual no está permitida en este entorno.';
        $messageType = 'error';
    } elseif ($mode === 'import') {
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
        $readme      = trim($_POST['readme'] ?? '');
        $selectedIcon = sanitizeInput($_POST['selected_icon'] ?? '');

        if (empty($title) || empty($description) || empty($author) || empty($category) || empty($ageGroup) || empty($githubLink)) {
            $message     = 'Todos los campos son obligatorios.';
            $messageType = 'error';
        } elseif (empty($selectedIcon)) {
            $message     = 'Debes elegir un icono predefinido para el juego.';
            $messageType = 'error';
        } elseif (!in_array($selectedIcon, $availableIcons, true)) {
            $message     = 'El icono seleccionado no está disponible en la carpeta de iconos.';
            $messageType = 'error';
        } elseif (!preg_match('/^https:\/\/[a-zA-Z0-9\-_]+\.github\.io\/[a-zA-Z0-9\-_\/]*$/', $githubLink)) {
            $message     = 'Por favor ingresa un enlace válido de GitHub Pages (debe contener github.io).';
            $messageType = 'error';
        } else {
            $folderName   = sanitizeFilename($title . '-' . time());
            $previewImage = $selectedIcon;

            if (empty($availableIcons)) {
                $message     = 'Aún no hay iconos cargados en la carpeta images/game-icons/. Subí al menos uno antes de publicar.';
                $messageType = 'error';
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO games (title, description, author, folder_name, category, age_group, github_link, readme, preview_image)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                if ($stmt->execute([$title, $description, $author, $folderName, $category, $ageGroup, $githubLink, $readme, $previewImage])) {
                    $message     = 'Juego registrado exitosamente. Está pendiente de aprobación.';
                    $messageType = 'success';
                    $title = $description = $author = $category = $ageGroup = $githubLink = $readme = $selectedIcon = '';
                } else {
                    $message     = 'Error al guardar en la base de datos.';
                    $messageType = 'error';
                }
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
    <link rel="stylesheet" href="css/style.css?v=2">
    <style>
        .icon-picker {
            position: relative;
            width: 100%;
        }

        .icon-picker-trigger {
            width: 100%;
            min-height: 70px;
            padding: 0.9rem 1rem;
            border-radius: 18px;
            border: 2px solid rgba(160, 174, 255, 0.7);
            background: rgba(32, 42, 61, 0.7);
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .icon-picker-trigger.selected {
            border-color: rgba(99, 163, 255, 0.95);
            box-shadow: 0 0 0 2px rgba(99, 163, 255, 0.18);
        }

        .icon-picker-trigger img {
            display: block;
            max-width: 100%;
            max-height: 54px;
            object-fit: contain;
        }

        .icon-picker-placeholder {
            color: rgba(203, 214, 234, 0.8);
            font-size: 1.05rem;
            font-weight: 600;
        }

        .icon-picker-options {
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            right: 0;
            border-radius: 18px;
            border: 2px solid rgba(160, 174, 255, 0.7);
            background: rgba(23, 32, 49, 0.98);
            box-shadow: 0 18px 32px rgba(0, 0, 0, 0.28);
            padding: 0.75rem;
            display: none;
            z-index: 20;
            max-height: 260px;
            overflow-y: auto;
        }

        .icon-picker-options.open {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            gap: 0.65rem;
        }

        .icon-option {
            background: rgba(49, 63, 91, 0.8);
            border: 2px solid transparent;
            border-radius: 14px;
            min-height: 90px;
            padding: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: border-color 0.2s ease, transform 0.15s ease, background 0.2s ease;
        }

        .icon-option:hover {
            border-color: rgba(160, 174, 255, 0.85);
            transform: translateY(-1px);
        }

        .icon-option.selected {
            border-color: rgba(99, 163, 255, 0.95);
            background: rgba(41, 110, 178, 0.28);
        }

        .icon-option img {
            display: block;
            max-width: 100%;
            max-height: 56px;
            object-fit: contain;
        }
    </style>
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
                    <?php if ($allow_repo): ?>
                    <button type="button" class="upload-tab <?= $activeTab === 'import' ? 'active' : '' ?>" data-tab="import" onclick="switchTab('import')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                        Importar Repo
                    </button>
                    <?php endif; ?>
                    <?php if ($allow_site): ?>
                    <button type="button" class="upload-tab <?= $activeTab === 'manual' ? 'active' : '' ?>" data-tab="manual" onclick="switchTab('manual')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Formulario
                    </button>
                    <?php endif; ?>
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

                    <form method="POST" enctype="multipart/form-data">
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

                        <div class="form-group">
                            <label class="form-label" for="readme">README del juego</label>
                            <textarea id="readme" name="readme" class="form-textarea"
                                placeholder="Escribe el README del juego en formato Markdown. Explica cómo jugar, de qué trata y los contenidos." rows="6"><?= htmlspecialchars($readme) ?></textarea>
                            <span class="form-hint">Opcional: se guardará como documentación del juego.</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="selected_icon">Icono predefinido</label>
                            <?php if (empty($availableIcons)): ?>
                                <div class="alert alert-error">Todavía no hay iconos cargados en la carpeta <strong>images/game-icons/</strong>. Subí al menos un icono para poder publicar.</div>
                            <?php else: ?>
                                <div class="icon-picker" id="iconPicker">
                                    <input type="hidden" id="selected_icon" name="selected_icon" value="<?= htmlspecialchars($selectedIcon) ?>" required>
                                    <button
                                        type="button"
                                        class="icon-picker-trigger <?= $selectedIcon ? 'selected' : '' ?>"
                                        id="iconPickerTrigger"
                                        aria-haspopup="listbox"
                                        aria-expanded="false"
                                    >
                                        <?php if ($selectedIcon): ?>
                                            <img src="images/game-icons/<?= htmlspecialchars($selectedIcon) ?>" alt="<?= htmlspecialchars($selectedIcon) ?>">
                                        <?php else: ?>
                                            <span class="icon-picker-placeholder">Seleccionar icono...</span>
                                        <?php endif; ?>
                                    </button>

                                    <div class="icon-picker-options" id="iconPickerOptions" role="listbox" aria-label="Iconos predefinidos">
                                        <?php foreach ($availableIcons as $icon): ?>
                                            <button
                                                type="button"
                                                class="icon-option <?= $selectedIcon === $icon ? 'selected' : '' ?>"
                                                data-icon="<?= htmlspecialchars($icon) ?>"
                                                role="option"
                                                aria-selected="<?= $selectedIcon === $icon ? 'true' : 'false' ?>"
                                            >
                                                <img src="images/game-icons/<?= htmlspecialchars($icon) ?>" alt="<?= htmlspecialchars($icon) ?>">
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <span class="form-hint">Debes elegir uno de los iconos disponibles en <strong>images/game-icons/</strong>.</span>
                            <?php endif; ?>
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
    document.querySelectorAll('.upload-tab').forEach(function(btn) {
        btn.classList.toggle('active', btn.getAttribute('data-tab') === tab);
    });
    document.querySelectorAll('.upload-tab-panel').forEach(function(panel) {
        panel.classList.toggle('active', panel.id === 'panel-' + tab);
    });
}

(function() {
    var iconPicker = document.getElementById('iconPicker');
    if (!iconPicker) {
        return;
    }

    var hiddenInput = document.getElementById('selected_icon');
    var trigger = document.getElementById('iconPickerTrigger');
    var optionsWrap = document.getElementById('iconPickerOptions');
    var options = optionsWrap ? optionsWrap.querySelectorAll('.icon-option') : [];

    function updateTrigger(icon) {
        if (!icon) {
            trigger.innerHTML = '<span class="icon-picker-placeholder">Seleccionar icono...</span>';
            trigger.classList.remove('selected');
            return;
        }

        trigger.innerHTML = '<img src="images/game-icons/' + icon + '" alt="' + icon + '">';
        trigger.classList.add('selected');
    }

    function setSelected(icon) {
        hiddenInput.value = icon || '';
        updateTrigger(icon);

        options.forEach(function(option) {
            var selected = option.dataset.icon === icon;
            option.classList.toggle('selected', selected);
            option.setAttribute('aria-selected', selected ? 'true' : 'false');
        });

        if (optionsWrap) {
            optionsWrap.classList.remove('open');
        }
        if (trigger) {
            trigger.setAttribute('aria-expanded', 'false');
        }
    }

    if (trigger) {
        trigger.addEventListener('click', function(event) {
            event.stopPropagation();
            var isOpen = optionsWrap.classList.contains('open');
            optionsWrap.classList.toggle('open', !isOpen);
            trigger.setAttribute('aria-expanded', String(!isOpen));
        });
    }

    options.forEach(function(option) {
        option.addEventListener('click', function() {
            setSelected(option.dataset.icon || '');
        });
    });

    document.addEventListener('click', function(event) {
        if (!iconPicker.contains(event.target)) {
            if (optionsWrap) {
                optionsWrap.classList.remove('open');
            }
            if (trigger) {
                trigger.setAttribute('aria-expanded', 'false');
            }
        }
    });
})();
</script>
</body>
</html>
