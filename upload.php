<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['creator', 'admin'])) {
    header('Location: login.php?redirect=upload.php');
    exit;
}

$message = '';
$messageType = '';
$title = '';
$description = '';
$author = '';
$category = '';
$ageGroup = '';
$githubLink = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $author = sanitizeInput($_POST['author']);
    $category = sanitizeInput($_POST['category']);
    $ageGroup = sanitizeInput($_POST['age_group']);
    $githubLink = sanitizeInput($_POST['github_link']);
    
    if (empty($title) || empty($description) || empty($author) || empty($category) || empty($ageGroup) || empty($githubLink)) {
        $message = 'Todos los campos son obligatorios.';
        $messageType = 'error';
    } elseif (!preg_match('/^https:\/\/[a-zA-Z0-9\-_]+\.github\.io\/[a-zA-Z0-9\-_\/]*$/', $githubLink)) {
        $message = 'Por favor ingresa un enlace válido de GitHub Pages (debe contener github.io).';
        $messageType = 'error';
    } else {
        // Generar un folder_name único basado en el título
        $folderName = sanitizeFilename($title . '-' . time());
        
        $stmt = $pdo->prepare("INSERT INTO games (title, description, author, folder_name, category, age_group, github_link) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $description, $author, $folderName, $category, $ageGroup, $githubLink])) {
            $message = 'Juego registrado exitosamente. Está pendiente de aprobación.';
            $messageType = 'success';
        } else {
            $message = 'Error al guardar en la base de datos.';
            $messageType = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Juego - Juegos Educativos</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="index.php"><i class="fas fa-arrow-left"></i> Volver</a></h1>
            <nav>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="login.php">Iniciar Sesión</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="retro-window">

            <!-- Barra de título al estilo retro -->
            <div class="retro-titlebar">
                <a href="index.php" class="retro-close-btn" title="Cancelar y volver">
                    <i class="fas fa-times"></i>
                </a>
                <span class="retro-titlebar-label">
                    <i class="fas fa-gamepad"></i> &nbsp;PUBLICAR JUEGO
                </span>
                <span class="retro-close-btn retro-close-btn--ghost" aria-hidden="true"></span>
            </div>

            <!-- Cuerpo de la ventana -->
            <div class="retro-window-body">

                <p class="retro-window-kicker">PUBLICA TU EXPERIENCIA EDUCATIVA EN LA COMUNIDAD ZELIA</p>

                <?php if ($message): ?>
                    <div class="message <?= $messageType ?>">
                        <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'triangle-exclamation' ?>"></i>
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="upload-form">

                    <div class="form-group">
                        <label for="title"><i class="fas fa-heading"></i> &nbsp;TITULO DEL JUEGO *</label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required maxlength="255" placeholder="Ejemplo: Laberinto de Fracciones">
                    </div>

                    <div class="form-group">
                        <label for="description"><i class="fas fa-align-left"></i> &nbsp;DESCRIPCION *</label>
                        <textarea id="description" name="description" required rows="3" placeholder="Explica que aprende el estudiante y como se juega."><?= htmlspecialchars($description) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="author"><i class="fas fa-user"></i> &nbsp;AUTOR *</label>
                        <input type="text" id="author" name="author" value="<?= htmlspecialchars($author) ?>" required maxlength="100" placeholder="Nombre o grupo creador">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="category"><i class="fas fa-tag"></i> &nbsp;CATEGORIA *</label>
                            <select id="category" name="category" required>
                                <option value="">[ SELECCIONAR ]</option>
                                <option value="Matemáticas" <?= $category === 'Matemáticas' ? 'selected' : '' ?>>Matematicas</option>
                                <option value="Lenguaje" <?= $category === 'Lenguaje' ? 'selected' : '' ?>>Lenguaje</option>
                                <option value="Ciencias" <?= $category === 'Ciencias' ? 'selected' : '' ?>>Ciencias</option>
                                <option value="Historia" <?= $category === 'Historia' ? 'selected' : '' ?>>Historia</option>
                                <option value="Geografía" <?= $category === 'Geografía' ? 'selected' : '' ?>>Geografia</option>
                                <option value="Arte" <?= $category === 'Arte' ? 'selected' : '' ?>>Arte</option>
                                <option value="Música" <?= $category === 'Música' ? 'selected' : '' ?>>Musica</option>
                                <option value="Lógica" <?= $category === 'Lógica' ? 'selected' : '' ?>>Logica</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="age_group"><i class="fas fa-child"></i> &nbsp;EDAD RECOMENDADA *</label>
                            <select id="age_group" name="age_group" required>
                                <option value="">[ SELECCIONAR ]</option>
                                <option value="3-5 años" <?= $ageGroup === '3-5 años' ? 'selected' : '' ?>>3-5 anos</option>
                                <option value="6-8 años" <?= $ageGroup === '6-8 años' ? 'selected' : '' ?>>6-8 anos</option>
                                <option value="9-12 años" <?= $ageGroup === '9-12 años' ? 'selected' : '' ?>>9-12 anos</option>
                                <option value="13-16 años" <?= $ageGroup === '13-16 años' ? 'selected' : '' ?>>13-16 anos</option>
                                <option value="17+ años" <?= $ageGroup === '17+ años' ? 'selected' : '' ?>>17+ anos</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="github_link"><i class="fab fa-github"></i> &nbsp;ENLACE GITHUB PAGES *</label>
                        <input type="url" id="github_link" name="github_link" value="<?= htmlspecialchars($githubLink) ?>" required placeholder="https://usuario.github.io/nombre-proyecto/" pattern="https://[a-zA-Z0-9\-_]+\.github\.io/.*">
                        <small><i class="fas fa-circle-info"></i> Formato: https://usuario.github.io/proyecto/</small>
                    </div>

                    <!-- Checklist integrado -->
                    <div class="retro-checklist">
                        <p class="retro-checklist-title"><i class="fas fa-list-check"></i> &nbsp;CHECKLIST DE PUBLICACION</p>
                        <ul>
                            <li><i class="fas fa-caret-right"></i> El juego debe estar alojado en GitHub Pages</li>
                            <li><i class="fas fa-caret-right"></i> URL con formato https://usuario.github.io/proyecto/</li>
                            <li><i class="fas fa-caret-right"></i> El enlace debe ser accesible y funcional</li>
                            <li><i class="fas fa-caret-right"></i> Contenido sera revisado antes de publicarse</li>
                            <li><i class="fas fa-caret-right"></i> Recursos externos deben cargar por HTTPS</li>
                        </ul>
                    </div>

                    <!-- Botones estilo OK / CANCEL -->
                    <div class="retro-actions">
                        <button type="submit" class="retro-btn retro-btn-ok">
                            <i class="fas fa-check"></i> OK
                        </button>
                        <a href="index.php" class="retro-btn retro-btn-cancel">
                            <i class="fas fa-times"></i> CANCEL
                        </a>
                    </div>

                </form>
            </div><!-- /retro-window-body -->
        </div><!-- /retro-window -->
    </main>

</body>
</html>