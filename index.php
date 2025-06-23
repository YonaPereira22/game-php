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
    <title>Juegos Educativos</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <h1><i class="fas fa-gamepad"></i> Juegos Educativos</h1>
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

    <main class="container">
        <section class="filters">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <input type="text" name="search" placeholder="Buscar juegos..." value="<?= htmlspecialchars($search) ?>">
                    
                    <select name="category">
                        <option value="">Todas las categorías</option>
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

        <section class="games-grid">
            <?php if (empty($games)): ?>
                <div class="no-games">
                    <i class="fas fa-sad-tear"></i>
                    <p>No se encontraron juegos con los filtros seleccionados.</p>
                </div>
            <?php else: ?>
                <?php foreach ($games as $game): ?>
                    <div class="game-card">
                        <div class="game-header">
                            <h3><?= htmlspecialchars($game['title']) ?></h3>
                            <div class="rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?= $i <= round($game['average_rating']) ? 'active' : '' ?>"></i>
                                <?php endfor; ?>
                                <span class="rating-text">
                                    <?= number_format($game['average_rating'], 1) ?> 
                                    (<?= $game['total_votes'] ?> votos)
                                </span>
                            </div>
                        </div>
                        
                        <div class="game-info">
                            <p class="description"><?= htmlspecialchars($game['description']) ?></p>
                            <div class="game-meta">
                                <span class="category"><i class="fas fa-tag"></i> <?= htmlspecialchars($game['category']) ?></span>
                                <span class="age"><i class="fas fa-child"></i> <?= htmlspecialchars($game['age_group']) ?></span>
                                <span class="author"><i class="fas fa-user"></i> <?= htmlspecialchars($game['author']) ?></span>
                            </div>
                        </div>
                        
                        <div class="game-actions">
                            <a href="game.php?id=<?= $game['id'] ?>" class="btn-play">
                                <i class="fas fa-play"></i> Jugar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Juegos Educativos. Plataforma segura para el aprendizaje.</p>
        </div>
    </footer>
</body>
</html>