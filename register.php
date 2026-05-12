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
    $username        = sanitizeInput($_POST['username']         ?? '');
    $email           = sanitizeInput($_POST['email']            ?? '');
    $password        = $_POST['password']                       ?? '';
    $confirmPassword = $_POST['confirm_password']               ?? '';
    $role            = sanitizeInput($_POST['role']             ?? 'student');

    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $message = 'Todos los campos son requeridos.';
        $messageType = 'error';
    } elseif (strlen($username) < 3) {
        $message = 'El usuario debe tener al menos 3 caracteres.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'El email no es válido.';
        $messageType = 'error';
    } elseif ($password !== $confirmPassword) {
        $message = 'Las contraseñas no coinciden.';
        $messageType = 'error';
    } elseif (strlen($password) < 6) {
        $message = 'La contraseña debe tener al menos 6 caracteres.';
        $messageType = 'error';
    } elseif (!in_array($role, ['creator', 'student'])) {
        $message = 'Rol inválido.';
        $messageType = 'error';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $message = 'El usuario o email ya existe.';
                $messageType = 'error';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $approved = ($role === 'student') ? true : false;

                $stmt = $pdo->prepare('INSERT INTO users (username, email, password, role, approved) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$username, $email, $hashedPassword, $role, $approved]);

                $message = '¡Registro exitoso! Redirigiendo al inicio de sesión…';
                $messageType = 'success';
                echo '<meta http-equiv="refresh" content="2;url=login.php">';
                $username = $email = $password = $confirmPassword = '';
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
    <title>Crear Cuenta — ZELIA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<main class="form-page">
    <div class="form-card">
        <h1 class="form-card-title">Crear cuenta</h1>
        <p class="form-card-subtitle">Únete a la comunidad ZELIA</p>

        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label" for="username">Nombre de usuario</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-input"
                    placeholder="mi_usuario"
                    value="<?= htmlspecialchars($username ?? '') ?>"
                    autocomplete="username"
                    required
                >
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    placeholder="correo@ejemplo.com"
                    value="<?= htmlspecialchars($email ?? '') ?>"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="password">Contraseña</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="••••••"
                        autocomplete="new-password"
                        required
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirmar</label>
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        class="form-input"
                        placeholder="••••••"
                        autocomplete="new-password"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Tipo de cuenta</label>
                <div class="role-options">
                    <div class="role-option">
                        <input type="radio" id="role_student" name="role" value="student" checked>
                        <label class="role-label" for="role_student">
                            <span class="role-label-name">🎓 Estudiante</span>
                            <span class="role-label-desc">Acceso inmediato</span>
                        </label>
                    </div>
                    <div class="role-option">
                        <input type="radio" id="role_creator" name="role" value="creator">
                        <label class="role-label" for="role_creator">
                            <span class="role-label-name">🛠️ Creador</span>
                            <span class="role-label-desc">Pendiente de aprobación</span>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="form-submit">Crear cuenta</button>
        </form>

        <p class="auth-footer">
            ¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a>
        </p>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>
