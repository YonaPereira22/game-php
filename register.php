<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Si ya está logueado, redirigir
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $role = sanitizeInput($_POST['role'] ?? 'student');

    // Validaciones
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $message = 'Todos los campos son requeridos';
        $messageType = 'error';
    } elseif (strlen($username) < 3) {
        $message = 'El usuario debe tener al menos 3 caracteres';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'El email no es válido';
        $messageType = 'error';
    } elseif ($password !== $confirmPassword) {
        $message = 'Las contraseñas no coinciden';
        $messageType = 'error';
    } elseif (strlen($password) < 6) {
        $message = 'La contraseña debe tener al menos 6 caracteres';
        $messageType = 'error';
    } elseif (!in_array($role, ['admin', 'creator', 'student'])) {
        $message = 'Rol inválido';
        $messageType = 'error';
    } else {
        try {
            // Verificar si el usuario o email ya existe
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $message = 'El usuario o email ya existe';
                $messageType = 'error';
            } else {
                // Hash la contraseña
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                
                // Determinar si debe ser aprobado automáticamente
                $approved = ($role === 'student') ? true : false;
                
                // Insertar nuevo usuario
                $stmt = $pdo->prepare('
                    INSERT INTO users (username, email, password, role, approved)
                    VALUES (?, ?, ?, ?, ?)
                ');
                $stmt->execute([$username, $email, $hashedPassword, $role, $approved]);
                
                $message = 'Registro exitoso. ¡Bienvenido! Ahora puedes iniciar sesión.';
                $messageType = 'success';
                
                // Limpiar los campos
                $username = $email = $password = $confirmPassword = '';
                
                // Redirigir después de 2 segundos
                echo '<meta http-equiv="refresh" content="2;url=login.php">';
            }
        } catch (PDOException $e) {
            $message = 'Error en el servidor: ' . $e->getMessage();
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
    <title>ZELIA - Crear Cuenta</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .role-options {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        .role-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .role-option input[type="radio"] {
            cursor: pointer;
        }
        .role-option label {
            cursor: pointer;
            margin: 0;
        }
        .form-info {
            background: #e8f4f8;
            border-left: 4px solid #0066cc;
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #333;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="index.php"><i class="fas fa-arrow-left"></i> Volver</a></h1>
            <h2>🎮 ZELIA - Crear Cuenta</h2>
        </div>
    </header>
    <main class="container">
        <?php if ($message): ?>
            <div class="message <?= htmlspecialchars($messageType) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="upload-form">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username ?? '') ?>" required minlength="3">
                <small>Mínimo 3 caracteres</small>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required minlength="6">
                <small>Mínimo 6 caracteres</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
            </div>
            
            <div class="form-group">
                <label>Tipo de Cuenta</label>
                <div class="role-options">
                    <div class="role-option">
                        <input type="radio" id="role_student" name="role" value="student" checked>
                        <label for="role_student">Estudiante</label>
                    </div>
                    <div class="role-option">
                        <input type="radio" id="role_creator" name="role" value="creator">
                        <label for="role_creator">Creador</label>
                    </div>
                </div>
                <div class="form-info">
                    <strong>Estudiante:</strong> Juega y vota juegos<br>
                    <strong>Creador:</strong> Sube y comparte tus juegos (requiere aprobación)
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">Registrarse</button>
            </div>
            
            <div style="text-align: center; margin-top: 15px;">
                <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
            </div>
        </form>
    </main>
</body>
</html>
