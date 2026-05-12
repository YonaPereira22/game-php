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
    <title>Feedback — ZELIA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<main class="feedback-page">
    <div class="container">

        <div class="page-header">
            <div class="section-badge">TU OPINIÓN IMPORTA</div>
            <h1 class="page-title">Feedback</h1>
            <p class="page-subtitle">Ayúdanos a mejorar la plataforma con tu experiencia</p>
        </div>

        <div class="feedback-card">
            <div class="feedback-card-header">
                <span class="feedback-icon">💬</span>
                <div>
                    <div class="feedback-card-title">Encuesta de experiencia</div>
                    <div class="feedback-card-subtitle">Tu opinión nos ayuda a mejorar ZELIA para todos</div>
                </div>
            </div>

            <div class="feedback-card-body">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <iframe
                        src="https://docs.google.com/forms/d/e/1FAIpQLSfPEzlKfk9NaUAGXFshgjtqC9tK5ebP51uAkozIKazNnDSsMA/viewform?embedded=true"
                        title="Encuesta de feedback ZELIA"
                        loading="lazy"
                        allowfullscreen
                    ></iframe>
                <?php else: ?>
                    <div class="feedback-gate">
                        <span class="gate-icon">🔒</span>
                        <h2 class="gate-title">Inicia sesión para participar</h2>
                        <p class="gate-desc">
                            Para completar la encuesta necesitás tener una cuenta en ZELIA.<br>
                            Es rápido, gratuito y te da acceso a todas las funcionalidades.
                        </p>
                        <div class="gate-actions">
                            <a href="login.php?redirect=feedback.php" class="btn btn-primary">Iniciar sesión</a>
                            <a href="register.php" class="btn btn-ghost">Crear cuenta</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>
