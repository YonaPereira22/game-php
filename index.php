<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';
$ageGroup = isset($_GET['age'])      ? sanitizeInput($_GET['age'])      : '';
$search   = isset($_GET['search'])   ? sanitizeInput($_GET['search'])   : '';

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

$sql    = $isAdmin ? "SELECT * FROM games" : "SELECT * FROM games WHERE approved = 1";
$params = [];

if ($category) { $sql .= " AND category = ?";                             $params[] = $category; }
if ($ageGroup)  { $sql .= " AND age_group = ?";                           $params[] = $ageGroup; }
if ($search)    { $sql .= " AND (title LIKE ? OR description LIKE ?)";    $params[] = "%$search%"; $params[] = "%$search%"; }

$sql .= " ORDER BY average_rating DESC, created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$games = $stmt->fetchAll();

$categoriesStmt = $pdo->query("SELECT DISTINCT category FROM games WHERE category IS NOT NULL");
$categories     = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);

$ageGroupsStmt = $pdo->query("SELECT DISTINCT age_group FROM games WHERE age_group IS NOT NULL");
$ageGroups     = $ageGroupsStmt->fetchAll(PDO::FETCH_COLUMN);

$totalStmt = $pdo->query("SELECT COUNT(*) FROM games WHERE approved = 1");
$totalGames = $totalStmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZELIA — Zona Educativa Lúdica con Inteligencia Artificial</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- HERO -->
<section class="hero">
    <div class="hero-glow"></div>
    <div class="container">
        <div class="hero-inner">
            <div class="hero-content">
                <div class="hero-badge">🎮 Plataforma educativa · 2026</div>
                <h1 class="hero-title">
                    Aprende<br>
                    <span class="gradient-text">jugando</span>
                </h1>
                <p class="hero-desc">
                    Explora juegos educativos diseñados para desarrollar habilidades digitales de forma divertida e interactiva.
                </p>
                <div class="hero-actions">
                    <a href="#juegos" class="btn btn-primary">Ver juegos</a>
                    <a href="nosotros.php" class="btn btn-ghost">Conocer el equipo</a>
                </div>
                <div class="hero-stats">
                    <div class="stat">
                        <strong><?= $totalGames ?></strong>
                        <span>Juegos</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat">
                        <strong><?= count($categories) ?></strong>
                        <span>Categorías</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat">
                        <strong>100%</strong>
                        <span>Gratuito</span>
                    </div>
                </div>
            </div>

            <div class="hero-visual">
                <div class="hero-circle"></div>
                <div class="floating-card fc1"><span>🧠</span><p>LÓGICA</p></div>
                <div class="floating-card fc2"><span>📐</span><p>MATEMÁTICAS</p></div>
                <div class="floating-card fc3"><span>🎯</span><p>ESTRATEGIA</p></div>
            </div>
        </div>
    </div>
</section>

<!-- FILTROS -->
<section class="search-section" id="juegos">
    <div class="container">
        <form method="GET" class="search-bar">
            <div class="search-input-wrap">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input
                    type="text"
                    name="search"
                    class="search-input"
                    placeholder="Buscar juegos…"
                    value="<?= htmlspecialchars($search) ?>"
                >
            </div>

            <div class="filter-chips">
                <a href="?<?= $ageGroup ? 'age='.urlencode($ageGroup) : '' ?>" class="chip<?= !$category ? ' active' : '' ?>">Todos</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="?category=<?= urlencode($cat) ?><?= $ageGroup ? '&age='.urlencode($ageGroup) : '' ?><?= $search ? '&search='.urlencode($search) : '' ?>"
                       class="chip<?= $category === $cat ? ' active' : '' ?>">
                        <?= htmlspecialchars($cat) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <select name="age" class="age-select" onchange="this.form.submit()">
                <option value="">Todas las edades</option>
                <?php foreach ($ageGroups as $age): ?>
                    <option value="<?= htmlspecialchars($age) ?>" <?= $ageGroup === $age ? 'selected' : '' ?>>
                        <?= htmlspecialchars($age) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
</section>

<!-- JUEGOS -->
<section class="games-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Juegos disponibles</h2>
            <span class="section-count"><?= count($games) ?></span>
        </div>

        <div class="games-grid">
            <?php if (empty($games)): ?>
                <div class="no-games">
                    <span style="font-size:48px">🔍</span>
                    <p>No se encontraron juegos con esos filtros.</p>
                </div>
            <?php else: ?>
                <?php
                $emojiMap = ['lógica'=>'🧩','matematicas'=>'📐','matemáticas'=>'📐','lenguaje'=>'📝','ciencias'=>'🔬','arte'=>'🎨','historia'=>'🏛️','programacion'=>'💻','programación'=>'💻'];
                foreach ($games as $game):
                    $cat    = strtolower($game['category'] ?? '');
                    $emoji  = $emojiMap[$cat] ?? '🎮';
                    $rating = round($game['average_rating'] ?? 0);
                    $stars  = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
                ?>
                <a href="game.php?id=<?= $game['id'] ?>" class="game-card<?= (!$game['approved']) ? ' game-card--pending' : '' ?>">
                    <?php if (!$game['approved']): ?>
                        <span class="game-pending-badge">PENDIENTE</span>
                    <?php endif; ?>
                    <div class="game-card-img">
                        <img
                            src="images/game-thumbnails/<?= htmlspecialchars($game['folder_name']) ?>.svg"
                            alt="<?= htmlspecialchars($game['title']) ?>"
                            class="game-thumbnail"
                            onerror="this.style.display='none';this.nextElementSibling.style.display='block'"
                        >
                        <span class="game-emoji" style="display:none"><?= $emoji ?></span>
                        <div class="game-card-overlay">
                            <span class="play-btn">▶ Jugar</span>
                        </div>
                    </div>
                    <div class="game-card-body">
                        <div class="game-card-meta">
                            <span class="game-badge"><?= htmlspecialchars($game['category']) ?></span>
                            <span class="game-age"><?= htmlspecialchars($game['age_group']) ?></span>
                        </div>
                        <div class="game-title"><?= htmlspecialchars($game['title']) ?></div>
                        <div class="game-desc"><?= htmlspecialchars($game['description']) ?></div>
                        <div class="game-footer">
                            <span class="stars"><?= $stars ?></span>
                            <span class="game-rating"><?= number_format($game['average_rating'] ?? 0, 1) ?></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<a href="feedback.php" class="feedback-float">Encuesta</a>

<?php include 'includes/footer.php'; ?>
</body>
</html>
