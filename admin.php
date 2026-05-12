<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php?redirect=admin.php');
    exit;
}

$tab        = $_GET['tab']         ?? 'games';
$action     = $_GET['action']      ?? '';
$gameId     = isset($_GET['id'])       ? (int)$_GET['id']       : 0;
$userAction = $_GET['user_action'] ?? '';
$userId     = isset($_GET['user_id'])  ? (int)$_GET['user_id']  : 0;

// ── Acciones de juegos ────────────────────────────────────────────────────────
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

// ── Acciones de usuarios ──────────────────────────────────────────────────────
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
        $update  = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $update->execute([$newRole, $userId]);
    }
    header('Location: admin.php?tab=users&msg=role_changed');
    exit;
}

if ($userAction === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = in_array($_POST['role'] ?? '', ['student','creator']) ? $_POST['role'] : 'student';
    if ($username && $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hash, $role]);
    }
    header('Location: admin.php?tab=users&msg=user_created');
    exit;
}

// ── Datos ─────────────────────────────────────────────────────────────────────
$stmt  = $pdo->query("SELECT * FROM games ORDER BY created_at DESC");
$games = $stmt->fetchAll();

$usersStmt = $pdo->query("SELECT id, username, email, role, approved FROM users ORDER BY username");
$users     = $usersStmt->fetchAll();

$approvedCount = count(array_filter($games, fn($g) => $g['approved']));
$pendingCount  = count(array_filter($games, fn($g) => !$g['approved']));
$totalVotes    = array_sum(array_column($games, 'total_votes'));

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin — ZELIA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<main class="admin-page">
    <div class="container">

        <div class="admin-header">
            <div>
                <h1 class="page-title" style="text-align:left">Panel de Administración</h1>
                <p class="page-subtitle" style="text-align:left">Gestión de juegos y usuarios</p>
            </div>
            <div class="admin-tabs">
                <a href="admin.php?tab=games" class="admin-tab <?= $tab === 'games' ? 'active' : '' ?>">Juegos</a>
                <a href="admin.php?tab=users" class="admin-tab <?= $tab === 'users' ? 'active' : '' ?>">Usuarios</a>
            </div>
        </div>

        <?php if ($msg): ?>
            <div class="alert alert-success" style="margin-bottom:20px">
                <?php
                $msgs = ['approved'=>'Juego aprobado.','deleted'=>'Juego eliminado.','user_approved'=>'Usuario aprobado.','role_changed'=>'Rol cambiado.','user_created'=>'Usuario creado.'];
                echo htmlspecialchars($msgs[$msg] ?? 'Acción realizada.');
                ?>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-icon">✅</div>
                <div>
                    <div class="stat-card-value"><?= $approvedCount ?></div>
                    <div class="stat-card-label">Juegos aprobados</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon">⏳</div>
                <div>
                    <div class="stat-card-value"><?= $pendingCount ?></div>
                    <div class="stat-card-label">Pendientes</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon">⭐</div>
                <div>
                    <div class="stat-card-value"><?= $totalVotes ?></div>
                    <div class="stat-card-label">Votos totales</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon">👥</div>
                <div>
                    <div class="stat-card-value"><?= count($users) ?></div>
                    <div class="stat-card-label">Usuarios</div>
                </div>
            </div>
        </div>

        <?php if ($tab === 'games'): ?>
        <!-- TABLA JUEGOS -->
        <div class="data-table-wrap">
            <div class="data-table-header">Todos los juegos</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Juego</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Votos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($games as $g): ?>
                    <tr>
                        <td>
                            <div style="font-weight:600"><?= htmlspecialchars($g['title']) ?></div>
                            <div style="font-size:12px;color:var(--text-dim)"><?= htmlspecialchars($g['author']) ?></div>
                        </td>
                        <td><?= htmlspecialchars($g['category']) ?></td>
                        <td>
                            <?php if ($g['approved']): ?>
                                <span class="status-badge approved">● Aprobado</span>
                            <?php else: ?>
                                <span class="status-badge pending">● Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td style="color:var(--text-muted)"><?= $g['total_votes'] ?></td>
                        <td>
                            <div class="table-actions">
                                <a href="game.php?id=<?= $g['id'] ?>" class="table-btn table-btn-view">Ver</a>
                                <?php if (!$g['approved']): ?>
                                    <a href="admin.php?action=approve&id=<?= $g['id'] ?>" class="table-btn table-btn-approve">Aprobar</a>
                                <?php endif; ?>
                                <a href="admin.php?action=delete&id=<?= $g['id'] ?>"
                                   class="table-btn table-btn-delete"
                                   onclick="return confirm('¿Eliminar este juego definitivamente?')">Eliminar</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php else: ?>
        <!-- TABLA USUARIOS -->
        <div class="data-table-wrap">
            <div class="data-table-header">Todos los usuarios</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td style="font-weight:600"><?= htmlspecialchars($u['username']) ?></td>
                        <td style="color:var(--text-muted);font-size:13px"><?= htmlspecialchars($u['email'] ?? '') ?></td>
                        <td>
                            <span class="game-badge" style="<?= $u['role'] === 'admin' ? 'color:var(--secondary);background:rgba(6,182,212,0.1);border-color:rgba(6,182,212,0.25)' : ($u['role'] === 'creator' ? 'color:var(--accent);background:rgba(16,185,129,0.1);border-color:rgba(16,185,129,0.25)' : '') ?>">
                                <?= htmlspecialchars($u['role']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($u['approved']): ?>
                                <span class="status-badge approved">● Activo</span>
                            <?php else: ?>
                                <span class="status-badge pending">● Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <?php if (!$u['approved']): ?>
                                    <a href="admin.php?tab=users&user_action=approve&user_id=<?= $u['id'] ?>" class="table-btn table-btn-approve">Aprobar</a>
                                <?php endif; ?>
                                <?php if ($u['role'] !== 'admin'): ?>
                                    <a href="admin.php?tab=users&user_action=toggle_role&user_id=<?= $u['id'] ?>" class="table-btn table-btn-edit">
                                        → <?= $u['role'] === 'student' ? 'Creador' : 'Estudiante' ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Crear usuario -->
        <div class="data-table-wrap" style="padding:0">
            <div class="data-table-header">Crear nuevo usuario</div>
            <div style="padding:24px 28px">
                <form method="POST" action="admin.php?tab=users&user_action=create" style="display:grid;grid-template-columns:1fr 1fr 160px auto;gap:12px;align-items:end">
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Usuario</label>
                        <input type="text" name="username" class="form-input" placeholder="nombre_usuario" required>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-input" placeholder="••••••" required>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Rol</label>
                        <select name="role" class="form-select">
                            <option value="student">Estudiante</option>
                            <option value="creator">Creador</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="height:44px;border-radius:var(--radius-sm)">Crear</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

    </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>
