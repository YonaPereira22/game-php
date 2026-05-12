<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros — ZELIA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- ABOUT -->
<section class="about-section">
    <div class="container">
        <div class="about-grid">
            <div>
                <span class="section-badge">¿QUIÉNES SOMOS?</span>
                <h2 class="about-title">
                    Aprendizaje a través<br>del <span class="gradient-text">juego</span>
                </h2>
                <p class="about-desc">
                    ZELIA (Zona Educativa Lúdica con Inteligencia Artificial) es una plataforma educativa desarrollada en el marco del Taller Integrador II del Profesorado de Informática del CeRP del Suroeste.
                </p>
                <p class="about-desc" style="margin-bottom:0">
                    Nuestro objetivo es ofrecer una colección de juegos educativos diseñados para que los estudiantes aprendan contenidos curriculares de forma interactiva, motivadora y accesible.
                </p>
            </div>
            <div class="about-cards">
                <div class="feat-card">
                    <span class="feat-icon">🎮</span>
                    <h4>Juegos educativos</h4>
                    <p>Contenidos curriculares integrados en experiencias de juego diseñadas por estudiantes.</p>
                </div>
                <div class="feat-card">
                    <span class="feat-icon">🧠</span>
                    <h4>Aprendizaje activo</h4>
                    <p>Metodologías que promueven el pensamiento crítico y la resolución de problemas.</p>
                </div>
                <div class="feat-card">
                    <span class="feat-icon">🌐</span>
                    <h4>Acceso libre</h4>
                    <p>Plataforma gratuita y accesible para toda la comunidad educativa.</p>
                </div>
                <div class="feat-card">
                    <span class="feat-icon">🤝</span>
                    <h4>Colaborativo</h4>
                    <p>Creado por estudiantes del profesorado, para estudiantes de todos los niveles.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- EQUIPO -->
<section class="team-section">
    <div class="container">
        <div class="team-section-header">
            <span class="section-badge">EL EQUIPO</span>
            <h2 class="team-section-title">Quienes hacen posible <span class="gradient-text">ZELIA</span></h2>
            <p class="team-section-desc">
                Un grupo de estudiantes y docentes del Profesorado de Informática trabajando juntos para innovar en la educación.
            </p>
        </div>

        <div class="team-grid">
            <!-- Docente -->
            <div class="team-card team-card--docente">
                <span class="team-avatar">👨‍🏫</span>
                <div class="team-name">Domingo Perez</div>
                <div class="team-role">Docente · Tutor</div>
            </div>

            <!-- Estudiantes -->
            <div class="team-card">
                <span class="team-avatar">👨‍💻</span>
                <div class="team-name">Yonhatan Pereira</div>
                <div class="team-role">Desarrollador</div>
            </div>
            <div class="team-card">
                <span class="team-avatar">👩‍💻</span>
                <div class="team-name">Lucía Rodríguez</div>
                <div class="team-role">Desarrolladora</div>
            </div>
            <div class="team-card">
                <span class="team-avatar">🧑‍💻</span>
                <div class="team-name">Emiliano Urruti</div>
                <div class="team-role">Desarrollador</div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
