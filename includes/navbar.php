<?php
$currentPage = basename($_SERVER['PHP_SELF']);
function navActive(string $page): string {
    global $currentPage;
    return $currentPage === $page ? ' active' : '';
}
?>
<header class="navbar" id="navbar">
    <div class="navbar-inner">
        <a href="index.php" class="navbar-brand">
            <span class="brand-icon">⬡</span>
            <span class="brand-name">ZELIA</span>
            <span class="brand-tag">EDU</span>
        </a>

        <nav class="navbar-links">
            <a href="index.php"     class="nav-link<?= navActive('index.php') ?>">Inicio</a>
            <a href="nosotros.php"  class="nav-link<?= navActive('nosotros.php') ?>">Nosotros</a>
            <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['creator', 'admin'])): ?>
                <a href="upload.php" class="nav-link<?= navActive('upload.php') ?>">Subir Juego</a>
            <?php endif; ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin.php" class="nav-link<?= navActive('admin.php') ?>">Admin</a>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="nav-link">Cerrar Sesión</a>
            <?php else: ?>
                <a href="login.php" class="nav-link btn-login">Iniciar Sesión</a>
            <?php endif; ?>
        </nav>

        <button class="hamburger" id="hamburger" aria-label="Menú">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<nav class="mobile-menu" id="mobileMenu">
    <a href="index.php"    class="nav-link<?= navActive('index.php') ?>">Inicio</a>
    <a href="nosotros.php" class="nav-link<?= navActive('nosotros.php') ?>">Nosotros</a>
    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['creator', 'admin'])): ?>
        <a href="upload.php" class="nav-link<?= navActive('upload.php') ?>">Subir Juego</a>
    <?php endif; ?>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="admin.php" class="nav-link<?= navActive('admin.php') ?>">Admin</a>
    <?php endif; ?>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="logout.php" class="nav-link">Cerrar Sesión</a>
    <?php else: ?>
        <a href="login.php" class="nav-link btn-login">Iniciar Sesión</a>
    <?php endif; ?>
</nav>
