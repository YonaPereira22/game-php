<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Juegos Educativos</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .feedback-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .avatar-section {
            background: var(--card);
            border: 2px solid var(--green);
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.2);
        }

        .avatar-wrapper {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 30px;
            align-items: start;
        }

        .avatar-image {
            text-align: center;
        }

        .avatar-image img {
            width: 200px;
            height: auto;
            image-rendering: pixelated;
            filter: drop-shadow(0 0 10px rgba(0, 255, 65, 0.4));
        }

        .avatar-message {
            background: rgba(0, 10, 26, 0.8);
            border: 2px solid var(--cyan);
            border-radius: 8px;
            padding: 20px;
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .message-box h3 {
            color: var(--yellow);
            font-family: 'Press Start 2P', monospace;
            font-size: 11px;
            margin-bottom: 15px;
            text-shadow: 0 0 8px var(--yellow);
        }

        .message-text {
            color: var(--green);
            font-family: 'VT323', monospace;
            font-size: 16px;
            line-height: 1.6;
            min-height: 80px;
            word-wrap: break-word;
        }

        .message-text.typing {
            border-right: 3px solid var(--green);
            animation: blink-cursor 1s step-end infinite;
        }

        @keyframes blink-cursor {
            0%, 49% { border-right-color: var(--green); }
            50%, 100% { border-right-color: transparent; }
        }

        .form-section {
            background: var(--card);
            border: 2px solid var(--green);
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.2);
        }

        .form-section h2 {
            color: var(--green);
            font-family: 'Press Start 2P', monospace;
            font-size: 14px;
            margin-bottom: 30px;
            text-shadow: 0 0 10px rgba(0, 255, 65, 0.3);
            border-bottom: 2px dashed rgba(0, 245, 255, 0.3);
            padding-bottom: 15px;
        }

        .form-section h2::before {
            content: '> ';
            color: var(--yellow);
        }

        iframe {
            width: 100%;
            height: 1200px;
            border: none;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .avatar-wrapper {
                grid-template-columns: 1fr;
            }

            .avatar-image {
                order: -1;
            }

            .feedback-container {
                margin: 20px auto;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <p class="blink">— INSERT COIN TO CONTINUE —</p>
            <h1>Juegos Educativos</h1>
            <p class="sub">APRENDE JUGANDO</p>
            <nav>
                <a href="index.php">Inicio</a>
                <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['creator', 'admin'])): ?>
                    <a href="upload.php">Subir Juego</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="admin.php">Admin</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="login.php">Iniciar Sesión</a>
                <?php endif; ?>
                <a href="feedback.php" style="margin-left: 250px;">Feedback</a>
            </nav>
        </div>
    </header>

    <main class="feedback-container">
        <div class="avatar-section">
            <div class="avatar-wrapper">
                <div class="avatar-image">
                    <img src="images/avatar.png" alt="Asistente Virtual" id="avatar">
                </div>
                <div class="message-box">
                    <h3>💬 ASISTENTE</h3>
                    <div class="message-text typing" id="messageText"></div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>ENVÍA TU FEEDBACK</h2>
            <iframe src="https://docs.google.com/forms/d/e/1FAIpQLSdho8SKx5GoBC1B9/viewform?embedded=true" frameborder="0" marginheight="0" marginwidth="0">Cargando…</iframe>
        </div>
    </main>

    <script>
        const messages = [
            "¡Hola! Soy tu asistente virtual. Tus comentarios son muy importantes para mejorar la plataforma.",
            "Queremos saber qué te pareció tu experiencia en los juegos educativos.",
            "Por favor, completa este formulario con tu feedback. ¡Gracias!",
            "Tu opinión nos ayuda a crear mejores contenidos educativos."
        ];

        let currentMessageIndex = 0;
        const messageElement = document.getElementById('messageText');
        const avatar = document.getElementById('avatar');

        function typeMessage(text) {
            messageElement.textContent = '';
            messageElement.classList.add('typing');
            let index = 0;

            const interval = setInterval(() => {
                if (index < text.length) {
                    messageElement.textContent += text[index];
                    index++;
                } else {
                    messageElement.classList.remove('typing');
                    clearInterval(interval);
                    setTimeout(() => {
                        currentMessageIndex = (currentMessageIndex + 1) % messages.length;
                        setTimeout(() => typeMessage(messages[currentMessageIndex]), 3000);
                    }, 3000);
                }
            }, 50);
        }

        // Iniciar con el primer mensaje
        typeMessage(messages[0]);

        // Agregar efecto de parpadeo y animación al avatar
        avatar.style.animation = 'none';
        setInterval(() => {
            avatar.style.opacity = '0.9';
            setTimeout(() => {
                avatar.style.opacity = '1';
            }, 100);
        }, 3000);
    </script>
</body>
</html>
