<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$gameId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$gameId) {
    header('Location: index.php');
    exit;
}

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if ($isAdmin) {
    $stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
} else {
    $stmt = $pdo->prepare("SELECT * FROM games WHERE id = ? AND approved = 1");
}
$stmt->execute([$gameId]);
$game = $stmt->fetch();

if (!$game) {
    header('Location: index.php');
    exit;
}

$userIP  = getUserIP();
$stmt    = $pdo->prepare("SELECT rating FROM votes WHERE game_id = ? AND user_ip = ?");
$stmt->execute([$gameId, $userIP]);
$userVote = $stmt->fetchColumn();

$rating  = round($game['average_rating'] ?? 0);
$stars   = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);

$gameSource  = '';
$sandboxAttr = 'allow-scripts allow-same-origin allow-forms allow-popups';
$folderPath  = 'games/' . $game['folder_name'];

if (!empty($game['github_link'])) {
    $gameSource = $game['github_link'];
} elseif (!empty($game['folder_name']) && is_dir($folderPath) && file_exists($folderPath . '/index.html')) {
    $gameSource = $folderPath . '/index.html';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($game['title']) ?> — ZELIA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<?php if ($isAdmin && !$game['approved']): ?>
    <div class="game-pending-notice">
        ⏳ Juego pendiente de aprobación
        <a href="admin.php?action=approve&id=<?= $game['id'] ?>" class="btn btn-success btn-sm" style="margin-left:auto">✓ Aprobar</a>
    </div>
<?php endif; ?>

<div class="game-layout">
    <!-- Sidebar -->
    <aside class="game-sidebar">
        <a href="index.php" class="back-link">← Volver</a>

        <div class="game-sidebar-title">Información del juego</div>

        <div class="game-info-item">
            <div class="game-info-label">Título</div>
            <div class="game-info-value"><?= htmlspecialchars($game['title']) ?></div>
        </div>

        <div class="game-info-item">
            <div class="game-info-label">Descripción</div>
            <div class="game-info-value" style="font-size:14px;color:var(--text-muted)"><?= htmlspecialchars($game['description']) ?></div>
        </div>

        <div class="game-info-item">
            <div class="game-info-label">Autor</div>
            <div class="game-info-value"><?= htmlspecialchars($game['author']) ?></div>
        </div>

        <div class="game-info-item">
            <div class="game-info-label">Categoría</div>
            <div class="game-info-value"><?= htmlspecialchars($game['category']) ?></div>
        </div>

        <div class="game-info-item">
            <div class="game-info-label">Edad recomendada</div>
            <div class="game-info-value"><?= htmlspecialchars($game['age_group']) ?></div>
        </div>

        <div class="game-info-item">
            <div class="game-info-label">Calificación</div>
            <div class="rating-display">
                <span class="rating-stars"><?= $stars ?></span>
                <span class="rating-value"><?= number_format($game['average_rating'] ?? 0, 1) ?> (<?= $game['total_votes'] ?> votos)</span>
            </div>
        </div>

        <div class="game-info-item" style="border-bottom:none">
            <div class="vote-section-title">Tu calificación</div>
            <div class="star-rating" data-game-id="<?= $game['id'] ?>">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="vote-star <?= ($userVote && $i <= $userVote) ? 'voted' : '' ?>"
                          data-rating="<?= $i ?>">★</span>
                <?php endfor; ?>
            </div>
            <?php if ($userVote): ?>
                <div class="user-vote-info">Tu voto: <?= $userVote ?> ★</div>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Game frame -->
    <div class="game-frame-container">
        <?php if ($gameSource): ?>
            <iframe
                src="<?= htmlspecialchars($gameSource) ?>"
                class="game-frame"
                sandbox="<?= $sandboxAttr ?>"
                loading="lazy"
                title="<?= htmlspecialchars($game['title']) ?>"
            ></iframe>
        <?php else: ?>
            <div class="game-error">
                <span class="game-error-icon">⚠️</span>
                <p>No se pudo cargar el juego. No hay carpeta local ni enlace de GitHub disponible.</p>
                <a href="index.php" class="btn btn-ghost" style="margin-top:8px">Volver al inicio</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="js/main.js"></script>
<?php include 'includes/footer.php'; ?>
</body>
</html>
