<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php?redirect=admin.php');
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$gameId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($action === 'approve' && $gameId) {
    $stmt = $pdo->prepare("UPDATE games SET approved = 1 WHERE id = ?");
    $stmt->execute([$gameId]);
    header('Location: admin.php?msg=approved');
    exit;
}

if ($action === 'delete' && $gameId) {
    $stmt = $pdo->prepare("SELECT folder_name FROM games WHERE id = ?");
    $stmt->execute([$gameId]);
    $folderName = $stmt->fetchColumn();
    
    if ($folderName) {
        $gameDir = 'games/' . $folderName;
        if (is_dir($gameDir)) {
            array_map('unlink', glob("$gameDir/*"));
            rmdir($gameDir);
        }
    }
    
    $stmt = $pdo->prepare("DELETE FROM games WHERE id = ?");
    $stmt->execute([$gameId]);
    header('Location: admin.php?msg=deleted');
    exit;
}

$stmt = $pdo->query("SELECT * FROM games ORDER BY created_at DESC");
$games = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Juegos Educativos</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="index.php"><i class="fas fa-arrow-left"></i> Volver</a></h1>
            <h2>Panel de Administración</h2>
            <nav>
                <a href="logout.php">Cerrar Sesión</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php if (isset($_GET['msg'])): ?>
            <div class="message success">
                <?php if ($_GET['msg'] === 'approved'): ?>
                    Juego aprobado exitosamente.
                <?php elseif ($_GET['msg'] === 'deleted'): ?>
                    Juego eliminado exitosamente.
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="admin-stats">
            <div class="stat-card">
                <i class="fas fa-gamepad"></i>
                <div>
                    <h3><?= count(array_filter($games, function($g) { return $g['approved']; })) ?></h3>
                    <p>Juegos Aprobados</p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <div>
                    <h3><?= count(array_filter($games, function($g) { return !$g['approved']; })) ?></h3>
                    <p>Pendientes</p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-star"></i>
                <div>
                    <h3><?= number_format(array_sum(array_column($games, 'total_votes'))) ?></h3>
                    <p>Votos Totales</p>
                </div>
            </div>
        </div>

        <div class="admin-table">
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Rating</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($games as $game): ?>
                        <tr>
                            <td><?= htmlspecialchars($game['title']) ?></td>
                            <td><?= htmlspecialchars($game['author']) ?></td>
                            <td><?= htmlspecialchars($game['category']) ?></td>
                            <td>
                                <span class="status <?= $game['approved'] ? 'approved' : 'pending' ?>">
                                    <?= $game['approved'] ? 'Aprobado' : 'Pendiente' ?>
                                </span>
                            </td>
                            <td>
                                <?= number_format($game['average_rating'], 1) ?> 
                                (<?= $game['total_votes'] ?>)
                            </td>
                            <td><?= date('d/m/Y', strtotime($game['created_at'])) ?></td>
                            <td class="actions">
                                <?php if (!$game['approved']): ?>
                                    <a href="?action=approve&id=<?= $game['id'] ?>" 
                                       class="btn-approve" 
                                       onclick="return confirm('¿Aprobar este juego?')">
                                        <i class="fas fa-check"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="game.php?id=<?= $game['id'] ?>" class="btn-view">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="?action=delete&id=<?= $game['id'] ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('¿Eliminar este juego permanentemente?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>