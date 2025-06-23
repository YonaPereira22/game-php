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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $author = sanitizeInput($_POST['author']);
    $category = sanitizeInput($_POST['category']);
    $ageGroup = sanitizeInput($_POST['age_group']);
    
    if (empty($title) || empty($description) || empty($author) || empty($category) || empty($ageGroup)) {
        $message = 'Todos los campos son obligatorios.';
        $messageType = 'error';
    } elseif (!isset($_FILES['game_file']) || $_FILES['game_file']['error'] !== UPLOAD_ERR_OK) {
        $message = 'Por favor selecciona un archivo ZIP válido.';
        $messageType = 'error';
    } else {
        $uploadedFile = $_FILES['game_file'];
        $folderName = sanitizeFilename($title . '-' . time());
        $tempDir = 'uploads/' . $folderName;
        $finalDir = 'games/' . $folderName;
        
        if (!is_dir('uploads')) mkdir('uploads', 0755, true);
        if (!is_dir('games')) mkdir('games', 0755, true);
        
        $zip = new ZipArchive();
        if ($zip->open($uploadedFile['tmp_name']) === TRUE) {
            $zip->extractTo($tempDir);
            $zip->close();
            
            if (validateGameFolder($tempDir)) {
                rename($tempDir, $finalDir);
                
                $stmt = $pdo->prepare("INSERT INTO games (title, description, author, folder_name, category, age_group) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$title, $description, $author, $folderName, $category, $ageGroup])) {
                    $message = 'Juego subido exitosamente. Está pendiente de aprobación.';
                    $messageType = 'success';
                } else {
                    $message = 'Error al guardar en la base de datos.';
                    $messageType = 'error';
                    if (is_dir($finalDir)) {
                        array_map('unlink', glob("$finalDir/*"));
                        rmdir($finalDir);
                    }
                }
            } else {
                $message = 'El juego no es válido. Debe contener un index.html y no puede tener scripts maliciosos.';
                $messageType = 'error';
                if (is_dir($tempDir)) {
                    array_map('unlink', glob("$tempDir/*"));
                    rmdir($tempDir);
                }
            }
        } else {
            $message = 'Error al extraer el archivo ZIP.';
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
        <?php if ($message): ?>
            <div class="message <?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="upload-form">
            <div class="form-group">
                <label for="title">Título del Juego *</label>
                <input type="text" id="title" name="title" required maxlength="255">
            </div>

            <div class="form-group">
                <label for="description">Descripción *</label>
                <textarea id="description" name="description" required rows="4"></textarea>
            </div>

            <div class="form-group">
                <label for="author">Autor *</label>
                <input type="text" id="author" name="author" required maxlength="100">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="category">Categoría *</label>
                    <select id="category" name="category" required>
                        <option value="">Seleccionar...</option>
                        <option value="Matemáticas">Matemáticas</option>
                        <option value="Lenguaje">Lenguaje</option>
                        <option value="Ciencias">Ciencias</option>
                        <option value="Historia">Historia</option>
                        <option value="Geografía">Geografía</option>
                        <option value="Arte">Arte</option>
                        <option value="Música">Música</option>
                        <option value="Lógica">Lógica</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="age_group">Edad Recomendada *</label>
                    <select id="age_group" name="age_group" required>
                        <option value="">Seleccionar...</option>
                        <option value="3-5 años">3-5 años</option>
                        <option value="6-8 años">6-8 años</option>
                        <option value="9-12 años">9-12 años</option>
                        <option value="13-16 años">13-16 años</option>
                        <option value="17+ años">17+ años</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="game_file">Archivo del Juego (ZIP) *</label>
                <input type="file" id="game_file" name="game_file" accept=".zip" required>
                <small>El archivo ZIP debe contener un index.html como archivo principal.</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-upload"></i> Subir Juego
                </button>
            </div>
        </form>

        <div class="upload-instructions">
            <h3><i class="fas fa-info-circle"></i> Instrucciones</h3>
            <ul>
                <li>El juego debe estar comprimido en un archivo ZIP</li>
                <li>Debe contener un archivo index.html como punto de entrada</li>
                <li>No se permiten scripts maliciosos o código dañino</li>
                <li>El juego será revisado antes de ser publicado</li>
                <li>Asegúrate de que sea contenido educativo apropiado</li>
            </ul>
        </div>
    </main>
</body>
</html>