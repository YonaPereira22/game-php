<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php?redirect=admin.php');
    exit;
}

$tab = $_GET['tab'] ?? 'games';
$action = $_GET['action'] ?? '';
$gameId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userAction = $_GET['user_action'] ?? '';
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

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

// User actions
if ($userAction === 'approve' && $userId) {
    $stmt = $pdo->prepare("UPDATE users SET approved = 1 WHERE id = ?");
    $stmt->execute([$userId]);
    header('Location: admin.php?tab=users&msg=user_approved');
    exit;
}

if ($userAction === 'toggle_role' && $userId) {
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $currentRole = $stmt->fetchColumn();
    if ($currentRole && $currentRole !== 'admin') {
        $newRole = $currentRole === 'student' ? 'creator' : 'student';
        $update = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $update->execute([$newRole, $userId]);
    }
    header('Location: admin.php?tab=users&msg=role_changed');
    exit;
}

if ($userAction === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = in_array($_POST['role'] ?? '', ['student','creator']) ? $_POST['role'] : 'student';
    if ($username && $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hash, $role]);
    }
    header('Location: admin.php?tab=users&msg=user_created');
    exit;
}

$stmt = $pdo->query("SELECT * FROM games ORDER BY created_at DESC");
$games = $stmt->fetchAll();

$usersStmt = $pdo->query("SELECT id, username, role, approved FROM users ORDER BY username");
$users = $usersStmt->fetchAll();
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
                <a href="admin.php?tab=games" class="<?= $tab === 'games' ? 'active' : '' ?>">Juegos</a>
                <a href="admin.php?tab=users" class="<?= $tab === 'users' ? 'active' : '' ?>">Usuarios</a>
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
                <?php elseif ($_GET['msg'] === 'user_approved'): ?>
                    Usuario aprobado exitosamente.
                <?php elseif ($_GET['msg'] === 'role_changed'): ?>
                    Rol actualizado.
                <?php elseif ($_GET['msg'] === 'user_created'): ?>
                    Usuario creado.
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php if ($tab === 'games'): ?>

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

    <?php else: ?>
        <div class="admin-table">
            <table>
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td>
                                <span class="status <?= $user['approved'] ? 'approved' : 'pending' ?>">
                                    <?= $user['approved'] ? 'Aprobado' : 'Pendiente' ?>
                                </span>
                            </td>
                            <td class="actions">
                                <?php if (!$user['approved']): ?>
                                    <a href="?tab=users&user_action=approve&user_id=<?= $user['id'] ?>" class="btn-approve" onclick="return confirm('¿Aprobar este usuario?')">
                                        <i class="fas fa-check"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($user['role'] !== 'admin'): ?>
                                    <a href="?tab=users&user_action=toggle_role&user_id=<?= $user['id'] ?>" class="btn-edit">
                                        <i class="fas fa-exchange-alt"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h3>Crear Nuevo Usuario</h3>
        <form method="POST" action="admin.php?tab=users&user_action=create" class="upload-form">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Rol</label>
                <select id="role" name="role">
                    <option value="student">Estudiante</option>
                    <option value="creator">Creador</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Crear</button>
            </div>
        </form>

    <?php endif; ?>
    </main>
    
</body>
</html>