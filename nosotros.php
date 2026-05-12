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
    <title>Nosotros - ZELIA</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css?v=<?= filemtime(__DIR__ . '/css/style.css') ?>">
    <style>
        .nosotros-container {
            max-width: 860px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .nosotros-title {
            font-family: 'Press Start 2P', monospace;
            font-size: clamp(14px, 3vw, 24px);
            color: var(--cyan);
            text-shadow: 0 0 10px var(--cyan), 0 0 30px var(--cyan);
            letter-spacing: 3px;
            margin-bottom: 12px;
            text-align: center;
        }

        .nosotros-subtitle {
            font-family: 'VT323', monospace;
            font-size: 20px;
            color: var(--green);
            text-align: center;
            letter-spacing: 4px;
            margin-bottom: 40px;
            opacity: 0.75;
        }

        .nosotros-section {
            background: var(--card);
            border: 1px solid var(--border);
            padding: 28px 32px;
            margin-bottom: 28px;
            position: relative;
        }

        .nosotros-section::before {
            content: '▶';
            position: absolute;
            top: -1px;
            left: 14px;
            font-size: 10px;
            color: var(--green);
            background: var(--card);
            padding: 0 6px;
            font-family: 'Press Start 2P', monospace;
        }

        .nosotros-section-title {
            font-family: 'Press Start 2P', monospace;
            font-size: 10px;
            color: var(--green);
            letter-spacing: 2px;
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }

        .nosotros-text {
            font-family: 'VT323', monospace;
            font-size: 20px;
            color: var(--green);
            line-height: 1.7;
            opacity: 0.9;
        }

        .nosotros-text a {
            color: var(--cyan);
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 18px;
            margin-top: 10px;
        }

        .team-card {
            background: var(--bg);
            border: 1px solid var(--green);
            padding: 20px 16px;
            text-align: center;
            transition: all 0.2s;
        }

        .team-card:hover {
            border-color: var(--cyan);
            box-shadow: 0 0 18px rgba(0, 245, 255, 0.2);
            transform: translateY(-3px);
        }

        .team-card-icon {
            font-size: 36px;
            margin-bottom: 10px;
            display: block;
        }

        .team-card-name {
            font-family: 'Press Start 2P', monospace;
            font-size: 7px;
            color: var(--cyan);
            letter-spacing: 1px;
            margin-bottom: 8px;
            line-height: 1.8;
        }

        .team-card-role {
            font-family: 'VT323', monospace;
            font-size: 16px;
            color: var(--green);
            opacity: 0.75;
            letter-spacing: 2px;
        }

        .team-card--docente .team-card-name {
            color: var(--yellow);
        }

        .team-card--docente {
            border-color: var(--yellow);
        }

        .team-card--docente:hover {
            border-color: var(--yellow);
            box-shadow: 0 0 18px rgba(255, 255, 0, 0.2);
        }

        .nosotros-list {
            font-family: 'VT323', monospace;
            font-size: 20px;
            color: var(--green);
            list-style: none;
            padding: 0;
            margin-top: 6px;
        }

        .nosotros-list li {
            padding: 6px 0;
            border-bottom: 1px dashed var(--border);
        }

        .nosotros-list li::before {
            content: '> ';
            color: var(--cyan);
        }

        .nosotros-list li:last-child {
            border-bottom: none;
        }

        body.theme-light .nosotros-section {
            background: var(--card);
            border-color: var(--border);
        }

        body.theme-light .nosotros-text,
        body.theme-light .team-card-role,
        body.theme-light .nosotros-list {
            color: var(--green);
        }

        body.theme-light .team-card {
            background: #f0f6f3;
        }

        @media (max-width: 600px) {
            .nosotros-section {
                padding: 20px 16px;
            }

            .team-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <p class="blink">— INSERTA LA FICHA AQUI—</p>
            <h1>🎮ZELIA</h1>
            <p class="sub">Zona Educativa Lúdica con Inteligencia Artificial - 2026</p>
            <nav>
                <a href="index.php">Inicio</a>
                <a href="nosotros.php">Nosotros</a>
                <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['creator', 'admin'])): ?>
                    <a href="upload.php">Subir Juego</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="admin.php">Admin</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="login.php" class="nav-login">Iniciar Sesión</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main>
        <div class="nosotros-container">
            <h2 class="nosotros-title">// NOSOTROS //</h2>
            <p class="nosotros-subtitle">QUIÉNES SOMOS Y QUÉ HACEMOS</p>

            <div class="nosotros-section">
                <h3 class="nosotros-section-title">SOBRE EL PROYECTO</h3>
                <p class="nosotros-text">
                    ZELIA es una <strong>consola educativa digital de videojuegos didácticos</strong> desarrollada en el marco de la unidad curricular
                    <em>Taller Integrador II – Proyecto de desarrollo de aplicaciones y videojuegos</em> del <strong>Profesorado de Informática</strong>.
                </p>
                <br>
                <p class="nosotros-text">
                    Nuestro objetivo es integrar diferentes juegos interactivos orientados al aprendizaje de contenidos vinculados a diversas especialidades,
                    aprovechando el potencial de los videojuegos como <strong>herramienta pedagógica</strong>, promoviendo el pensamiento computacional
                    y la participación activa de los estudiantes.
                </p>
            </div>

            <div class="nosotros-section">
                <h3 class="nosotros-section-title">EL EQUIPO</h3>
                <div class="team-grid">
                    <div class="team-card team-card--docente">
                        <span class="team-card-icon">👨‍🏫</span>
                        <p class="team-card-name">Domingo Perez</p>
                        <p class="team-card-role">DOCENTE</p>
                    </div>
                    <div class="team-card">
                        <span class="team-card-icon">👨‍💻</span>
                        <p class="team-card-name">Yonhatan Pereira</p>
                        <p class="team-card-role">ESTUDIANTE</p>
                    </div>
                    <div class="team-card">
                        <span class="team-card-icon">👩‍💻</span>
                        <p class="team-card-name">Lucía Rodríguez</p>
                        <p class="team-card-role">ESTUDIANTE</p>
                    </div>
                    <div class="team-card">
                        <span class="team-card-icon">👨‍💻</span>
                        <p class="team-card-name">Emiliano Urruti</p>
                        <p class="team-card-role">ESTUDIANTE</p>
                    </div>
                </div>
            </div>

            <div class="nosotros-section">
                <h3 class="nosotros-section-title">NUESTROS OBJETIVOS</h3>
                <ul class="nosotros-list">
                    <li>Diseñar videojuegos educativos con enfoque pedagógico</li>
                    <li>Integrar contenidos de distintas áreas de formación</li>
                    <li>Promover el pensamiento computacional en el aula</li>
                    <li>Crear recursos digitales innovadores para la enseñanza</li>
                    <li>Fomentar la participación activa de los estudiantes</li>
                </ul>
            </div>

            <div class="nosotros-section">
                <h3 class="nosotros-section-title">CONTEXTO ACADÉMICO</h3>
                <p class="nosotros-text">
                    Este proyecto fue desarrollado en el año <strong>2026</strong> como parte de la propuesta curricular del
                    Profesorado de Informática. Busca aportar al desarrollo de recursos educativos innovadores dentro del
                    ámbito de la enseñanza, articulando la tecnología con la práctica docente.
                </p>
            </div>

            <div style="text-align:center; margin-top: 30px;">
                <a href="index.php" class="back-link">◀ VOLVER AL LOBBY</a>
            </div>
        </div>
    </main>

    <footer style="text-align:center; padding:30px; font-family:'VT323',monospace; font-size:16px; color:rgba(0,255,65,.4); border-top:1px solid var(--border); margin-top:40px;">
        &copy; 2026 ZELIA &mdash; Taller Integrador II &mdash; Profesorado de Informática
    </footer>

    <script src="js/theme-toggle.js"></script>
</body>
</html>
