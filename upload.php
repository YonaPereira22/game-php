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
<body class="upload-page">
    <header>
        <div class="container">
            <h1><a href="index.php"><i class="fas fa-arrow-left"></i> Volver</a></h1>
            <h2>Subir Nuevo Juego</h2>
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
        <section class="upload-hero">
            <p class="upload-kicker">PUBLICA TU EXPERIENCIA EDUCATIVA</p>
            <h2 class="upload-title">Sube tu juego y compártelo con toda la comunidad ZELIA</h2>
            <p class="upload-subtitle">Mantuvimos la vibra retro, pero con un flujo mas claro para publicar rapido y sin errores.</p>
        </section>

        <?php if ($message): ?>
            <div class="message <?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <section class="upload-layout">
            <form method="POST" class="upload-form upload-card">
                <h3 class="upload-form-title">Datos del Juego</h3>
                <p class="upload-form-subtitle">Completa la ficha con informacion clara para que el equipo pueda aprobarlo rapidamente.</p>

                <div class="form-group">
                    <label for="title">Titulo del Juego *</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required maxlength="255" placeholder="Ejemplo: Laberinto de Fracciones">
                </div>

                <div class="form-group">
                    <label for="description">Descripcion *</label>
                    <textarea id="description" name="description" required rows="4" placeholder="Explica que aprende el estudiante y como se juega."><?= htmlspecialchars($description) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="author">Autor *</label>
                    <input type="text" id="author" name="author" value="<?= htmlspecialchars($author) ?>" required maxlength="100" placeholder="Nombre o grupo creador">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Categoria *</label>
                        <select id="category" name="category" required>
                            <option value="">Seleccionar...</option>
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
                        <label for="age_group">Edad Recomendada *</label>
                        <select id="age_group" name="age_group" required>
                            <option value="">Seleccionar...</option>
                            <option value="3-5 años" <?= $ageGroup === '3-5 años' ? 'selected' : '' ?>>3-5 anos</option>
                            <option value="6-8 años" <?= $ageGroup === '6-8 años' ? 'selected' : '' ?>>6-8 anos</option>
                            <option value="9-12 años" <?= $ageGroup === '9-12 años' ? 'selected' : '' ?>>9-12 anos</option>
                            <option value="13-16 años" <?= $ageGroup === '13-16 años' ? 'selected' : '' ?>>13-16 anos</option>
                            <option value="17+ años" <?= $ageGroup === '17+ años' ? 'selected' : '' ?>>17+ anos</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="github_link">Enlace de GitHub Pages *</label>
                    <input type="url" id="github_link" name="github_link" value="<?= htmlspecialchars($githubLink) ?>" required placeholder="https://usuario.github.io/nombre-proyecto/" pattern="https://[a-zA-Z0-9\-_]+\.github\.io/.*">
                    <small>Debe ser un enlace de GitHub Pages y contener github.io.</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-upload"></i> Publicar Juego
                    </button>
                </div>
            </form>

            <aside class="upload-instructions upload-card">
                <h3><i class="fas fa-info-circle"></i> Checklist de Publicacion</h3>
                <ul>
                    <li>El juego debe estar alojado en GitHub Pages.</li>
                    <li>El enlace debe seguir formato https://usuario.github.io/proyecto/.</li>
                    <li>Comprueba que el enlace sea accesible y funcional.</li>
                    <li>El contenido sera revisado antes de publicarse.</li>
                    <li>Incluye enfoque educativo y lenguaje apropiado.</li>
                </ul>
                <div class="upload-tip-box">
                    <p><strong>Tip:</strong> si tu juego usa recursos externos, verifica que carguen por HTTPS para evitar bloqueos del navegador.</p>
                </div>
            </aside>
        </section>
    </main>
    
</body>
</html>