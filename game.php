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
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="index.php"><i class="fas fa-arrow-left"></i> Volver</a></h1>
            <h2><?= htmlspecialchars($game['title']) ?></h2>
            <nav>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="login.php">Iniciar Sesión</a>
                <?php endif; ?>
            </nav>
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
            <iframe 
                src="games/<?= htmlspecialchars($game['folder_name']) ?>/index.html" 
                class="game-frame"
                sandbox="allow-scripts allow-same-origin allow-forms"
                loading="lazy">
            </iframe>
        </div>
    </main>

    <script src="js/main.js"></script>
</body>
</html>