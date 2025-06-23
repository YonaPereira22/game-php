<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, password, role FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $user['role'];
        $redirect = $_GET['redirect'] ?? 'index.php';
        header('Location: ' . $redirect);
        exit;
    } else {
        $message = 'Credenciales incorrectas';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="index.php"><i class="fas fa-arrow-left"></i> Volver</a></h1>
            <h2>Iniciar Sesión</h2>
        </div>
    </header>
    <main class="container">
        <?php if ($message): ?>
            <div class="message error">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <form method="POST" class="upload-form">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Ingresar</button>
            </div>
        </form>
    </main>
</body>
</html>

