<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $message = 'Usuario y contraseña requeridos.';
        $messageType = 'error';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id, password, role, approved FROM users WHERE username = ? OR email = ?');
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                if (!$user['approved']) {
                    $message = 'Tu cuenta está pendiente de aprobación. Por favor, espera la confirmación del administrador.';
                    $messageType = 'error';
                } else {
                    $_SESSION['user_id']  = $user['id'];
                    $_SESSION['username'] = $username;
                    $_SESSION['role']     = $user['role'];

                    $updateStmt = $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = ?');
                    $updateStmt->execute([$user['id']]);

                    $redirect = $_GET['redirect'] ?? 'index.php';
                    header('Location: ' . $redirect);
                    exit;
                }
            } else {
                $message = 'Usuario o contraseña incorrectos.';
                $messageType = 'error';
            }
        } catch (PDOException $e) {
            $message = 'Error en el servidor.';
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
    <title>Iniciar Sesión — ZELIA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<main class="form-page">
    <div class="form-card">
        <h1 class="form-card-title">Bienvenido de vuelta</h1>
        <p class="form-card-subtitle">Inicia sesión en tu cuenta ZELIA</p>

        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <a href="google_login.php" class="google-btn">
            <svg width="18" height="18" viewBox="0 0 18 18"><path fill="#4285F4" d="M16.51 8H8.98v3h4.3c-.18 1-.74 1.48-1.6 2.04v2.01h2.6a7.8 7.8 0 0 0 2.38-5.88c0-.57-.05-.66-.15-1.18z"/><path fill="#34A853" d="M8.98 17c2.16 0 3.97-.72 5.3-1.94l-2.6-2a4.8 4.8 0 0 1-7.18-2.54H1.83v2.07A8 8 0 0 0 8.98 17z"/><path fill="#FBBC05" d="M4.5 10.52a4.8 4.8 0 0 1 0-3.04V5.41H1.83a8 8 0 0 0 0 7.18z"/><path fill="#EA4335" d="M8.98 4.18c1.17 0 2.23.4 3.06 1.2l2.3-2.3A8 8 0 0 0 1.83 5.4L4.5 7.49a4.77 4.77 0 0 1 4.48-3.3z"/></svg>
            Continuar con Google
        </a>

        <div class="divider">o con tu cuenta</div>

        <form method="POST">
            <div class="form-group">
                <label class="form-label" for="username">Usuario o Email</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-input"
                    placeholder="tu_usuario"
                    autocomplete="username"
                    required
                >
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    required
                >
            </div>
            <button type="submit" class="form-submit">Iniciar Sesión</button>
        </form>

        <p class="auth-footer">
            ¿No tienes cuenta? <a href="register.php">Crear una cuenta</a>
        </p>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>
