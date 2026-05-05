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
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $message = 'Usuario y contraseña requeridos';
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
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $user['role'];
                    
                    // Actualizar último acceso
                    $updateStmt = $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = ?');
                    $updateStmt->execute([$user['id']]);
                    
                    $redirect = $_GET['redirect'] ?? 'index.php';
                    header('Location: ' . $redirect);
                    exit;
                }
            } else {
                $message = 'Usuario o contraseña incorrectos';
                $messageType = 'error';
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
    <title>ZELIA - Iniciar Sesión</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 450px;
            margin: 40px auto;
        }
        .auth-form {
            background: #fff;
            border: 2px solid #333;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #ddd;
        }
        .divider span {
            background: #fff;
            padding: 0 10px;
            position: relative;
            z-index: 1;
            color: #666;
            font-size: 14px;
        }
        .google-login {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px;
            background: #fff;
            border: 2px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            margin-bottom: 20px;
        }
        .google-login:hover {
            border-color: #4285f4;
            background: #f9f9f9;
            box-shadow: 0 2px 4px rgba(66, 133, 244, 0.3);
        }
        .google-login img {
            width: 20px;
            height: 20px;
        }
        .auth-links {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        .auth-links a {
            color: #0066cc;
            text-decoration: none;
            font-weight: 500;
        }
        .auth-links a:hover {
            text-decoration: underline;
        }
        .message {
            padding: 12px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }
        .message.error {
            background: #fee;
            border-left-color: #f00;
            color: #c00;
        }
        .message.success {
            background: #efe;
            border-left-color: #0f0;
            color: #060;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="index.php"><i class="fas fa-arrow-left"></i> Volver</a></h1>
            <h2>🎮 ZELIA - Iniciar Sesión</h2>
        </div>
    </header>
    <main class="container login-container">
        <?php if ($message): ?>
            <div class="message <?= htmlspecialchars($messageType) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <div class="auth-form">
            <!-- Botón de Google OAuth -->
            <a href="google_login.php" class="google-login">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_3844_7657)">
                        <path d="M23.745 12.27c0-.79-.1-1.54-.257-2.26H12v4.26h6.899c-.29 1.48-1.144 2.73-2.404 3.58v3.01h3.89c2.27-2.09 3.576-5.17 3.576-8.59z" fill="#4285F4"/>
                        <path d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.01c-1.08.72-2.45 1.13-4.05 1.13-3.12 0-5.78-2.11-6.73-4.96h-3.98v3.1C3.43 23.06 7.33 24 12 24z" fill="#34A853"/>
                        <path d="M5.27 14.25c-.27-.72-.42-1.49-.42-2.25s.15-1.53.42-2.25V6.65h-3.98a11.966 11.966 0 000 10.7l3.98-3.1z" fill="#FBBC05"/>
                        <path d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.44-3.44C17.94 1.12 15.23 0 12 0 7.33 0 3.43.97 1.29 3.65l3.98 3.1c.95-2.85 3.61-4.96 6.73-4.96z" fill="#EA4335"/>
                    </g>
                </svg>
                Inicia sesión con Google
            </a>
            
            <div class="divider">
                <span>o con tu cuenta</span>
            </div>
            
            <!-- Formulario de login tradicional -->
            <form method="POST">
                <div class="form-group">
                    <label for="username">Usuario o Email</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary" style="width: 100%;">Ingresar</button>
                </div>
            </form>
            
            <div class="auth-links">
                <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
            </div>
        </div>
    </main>
    <script src="js/theme-toggle.js"></script>
</body>
</html>


