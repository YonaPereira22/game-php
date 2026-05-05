<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$gameId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$gameId) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM games WHERE id = ? AND approved = 1");
$stmt->execute([$gameId]);
$game = $stmt->fetch();

if (!$game) {
    header('Location: index.php');
    exit;
}

$userIP = getUserIP();
$stmt = $pdo->prepare("SELECT rating FROM votes WHERE game_id = ? AND user_ip = ?");
$stmt->execute([$gameId, $userIP]);
$userVote = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($game['title']) ?> - Juegos Educativos</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="back-link">◀ VOLVER AL LOBBY</a>
            <h1 style="margin: 10px 0; text-shadow: 0 0 10px var(--green);"><?= htmlspecialchars($game['title']) ?></h1>
        </div>
    </header>

    <main class="game-container">
        <div class="game-sidebar">
            <div class="game-info-detailed">
                <h3>Información del Juego</h3>
                <p><strong>Descripción:</strong> <?= htmlspecialchars($game['description']) ?></p>
                <p><strong>Autor:</strong> <?= htmlspecialchars($game['author']) ?></p>
                <p><strong>Categoría:</strong> <?= htmlspecialchars($game['category']) ?></p>
                <p><strong>Edad recomendada:</strong> <?= htmlspecialchars($game['age_group']) ?></p>
                
                <div class="rating-section">
                    <h4>Calificación</h4>
                    <div class="current-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?= $i <= round($game['average_rating']) ? 'active' : '' ?>"></i>
                        <?php endfor; ?>
                        <span><?= number_format($game['average_rating'], 1) ?> (<?= $game['total_votes'] ?> votos)</span>
                    </div>
                    
                    <div class="vote-section">
                        <h5>Tu calificación:</h5>
                        <div class="star-rating" data-game-id="<?= $game['id'] ?>">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star vote-star <?= $userVote && $i <= $userVote ? 'voted' : '' ?>" 
                                   data-rating="<?= $i ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <?php if ($userVote): ?>
                            <p class="user-vote-info">Ya votaste: <?= $userVote ?> estrellas</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="game-frame-container">
            <?php
            // Determinar la fuente del juego: carpeta local o GitHub Pages
            $gameSource = '';
            $folderPath = 'games/' . $game['folder_name'];
            
            // Primero intentar usar la carpeta local si existe
            if (!empty($game['folder_name']) && is_dir($folderPath) && file_exists($folderPath . '/index.html')) {
                $gameSource = $folderPath . '/index.html';
            } 
            // Si no hay carpeta, usar el enlace de GitHub
            elseif (!empty($game['github_link'])) {
                $gameSource = $game['github_link'];
            }
            ?>
            
            <?php if ($gameSource): ?>
                <iframe 
                    src="<?= htmlspecialchars($gameSource) ?>" 
                    class="game-frame"
                    sandbox="allow-scripts allow-same-origin allow-forms"
                    loading="lazy">
                </iframe>
            <?php else: ?>
                <div class="game-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>No se pudo cargar el juego. No hay carpeta local ni enlace de GitHub disponible.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="js/theme-toggle.js"></script>
    <script src="js/main.js"></script>
</body>
</html>