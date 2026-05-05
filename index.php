<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';
$ageGroup = isset($_GET['age']) ? sanitizeInput($_GET['age']) : '';
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

$sql = "SELECT * FROM games WHERE approved = 1";
$params = [];

if ($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

if ($ageGroup) {
    $sql .= " AND age_group = ?";
    $params[] = $ageGroup;
}

if ($search) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY average_rating DESC, created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$games = $stmt->fetchAll();

$categoriesStmt = $pdo->query("SELECT DISTINCT category FROM games WHERE approved = 1 AND category IS NOT NULL");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);

$ageGroupsStmt = $pdo->query("SELECT DISTINCT age_group FROM games WHERE approved = 1 AND age_group IS NOT NULL");
$ageGroups = $ageGroupsStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZELIA - Zona Educativa Lúdica con Inteligencia Artificial - 2026</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css?v=<?= filemtime(__DIR__ . '/css/style.css') ?>">
    <style>
        .feedback-float {
            position: fixed;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10050;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 15px 18px;
            border: 2px solid #ffe600;
            border-radius: 14px;
            background: #1a1030;
            color: #ffe600;
            text-decoration: none;
            font-family: 'Press Start 2P', monospace;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-shadow: 0 0 8px rgba(255, 255, 0, 0.8);
            box-shadow: 0 0 16px rgba(255, 255, 0, 0.45), inset 0 0 14px rgba(255, 255, 0, 0.2);
            animation: feedback-float-bob 2.2s ease-in-out infinite, feedback-pulse 1.4s ease-in-out infinite;
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        .feedback-float::before {
            content: '●';
            color: #ff3355;
            font-size: 12px;
            animation: feedback-alert-dot 0.9s steps(2, end) infinite;
        }

        .feedback-float:hover {
            transform: translateY(-50%) translateX(8px) scale(1.03);
            box-shadow: 0 0 22px rgba(255, 255, 0, 0.75), inset 0 0 16px rgba(255, 255, 0, 0.35);
            filter: saturate(1.2);
        }

        @keyframes feedback-float-bob {
            0%,
            100% {
                transform: translateY(-50%) translateX(0);
            }
            50% {
                transform: translateY(calc(-50% - 6px)) translateX(0);
            }
        }

        @keyframes feedback-pulse {
            0%,
            100% {
                box-shadow: 0 0 14px rgba(255, 255, 0, 0.4), inset 0 0 12px rgba(255, 255, 0, 0.18);
            }
            50% {
                box-shadow: 0 0 26px rgba(255, 255, 0, 0.85), inset 0 0 20px rgba(255, 255, 0, 0.35);
            }
        }

        @keyframes feedback-alert-dot {
            0%,
            49% {
                opacity: 1;
            }
            50%,
            100% {
                opacity: 0.25;
            }
        }

        @media (max-width: 768px) {
            .feedback-float {
                left: 8px;
                padding: 11px 12px;
                font-size: 8px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <p class="blink">— INSERTA LA FICHA AQUI—</p>
            <h1>🎮 ZELIA</h1>
            <p class="sub">Zona Educativa Lúdica con Inteligencia Artificial - 2026</p>
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
            </nav>
        </div>
    </header>

    <a href="feedback.php" class="feedback-float">Encuesta</a>

    <main class="container">
        <p class="section-label">ELIGE TU JUEGO Y PULSA ▶ JUGAR</p>
        <section class="filters">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <input type="text" name="search" placeholder="Buscar juegos...." value="<?= htmlspecialchars($search) ?>">
                    
                    <select name="category">
                        <option value="">Categorías</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="age">
                        <option value="">Todas las edades</option>
                        <?php foreach ($ageGroups as $age): ?>
                            <option value="<?= htmlspecialchars($age) ?>" <?= $ageGroup === $age ? 'selected' : '' ?>>
                                <?= htmlspecialchars($age) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button type="submit"><i class="fas fa-search"></i> Filtrar</button>
                </div>
            </form>
        </section>

        <section class="grid">
            <?php if (empty($games)): ?>
                <div class="no-games">
                    <i class="fas fa-sad-tear"></i>
                    <p>No se encontraron juegos con los filtros seleccionados.</p>
                </div>
            <?php else: ?>
                <?php foreach ($games as $game): ?>
                    <a href="game.php?id=<?= $game['id'] ?>" class="game-card">
                        <div class="cscreen" style="background:#001a00">
                            <img src="images/game-thumbnails/<?= htmlspecialchars($game['folder_name']) ?>.svg" alt="<?= htmlspecialchars($game['title']) ?>" class="game-thumbnail" onerror="this.src='images/game-thumbnails/default.svg'">
                            <div class="cov">
                                <span class="cov-btn">▶ JUGAR</span>
                            </div>
                        </div>
                        <div class="cinfo">
                            <div class="ctitle"><?= htmlspecialchars($game['title']) ?></div>
                            <div class="cmeta">
                                <span style="color:var(--cyan)"><?= htmlspecialchars($game['category']) ?></span>
                                <span class="cbadge" style="color:#88cc44;border-color:#88cc44"><?= htmlspecialchars($game['age_group']) ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 Juegos Educativos CeRP del Suroeste. Plataforma segura para el aprendizaje.</p>
        </div>
    </footer>

    <script src="js/theme-toggle.js"></script>
</body>
</html>
