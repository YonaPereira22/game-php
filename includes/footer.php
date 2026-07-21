<?php
// Variables de entorno: se leen con getenv().
// Definición recomendada: en el servidor (export APP_VERSION=1.2) o mediante
// un archivo opcional `config/env.php` que establezca defaults para desarrollo.
// Variables usadas actualmente:
// - UPLOAD_TYPES: 'repo' | 'site' | 'both' (controla opciones de subida en upload.php)
// - APP_VERSION: versión de la aplicación/archivo (ej: '1.2')
$app_version = getenv('APP_VERSION') ?: '1.00'; // Fase 2 = 1.00 por defecto
?>

<footer class="site-footer">
    <div class="container">
        <div class="footer-inner">
            <div class="footer-brand">
                <span style="font-size:22px;color:var(--primary);filter:drop-shadow(0 0 8px rgba(139,92,246,0.5))">⬡</span>
                <span class="footer-brand-name">ZELIA</span>
            </div>
            <p class="footer-desc">Zona Educativa Lúdica con Inteligencia Artificial</p>
            <p class="footer-copy">© 2026 · Taller Integrador II · Profesorado de Informática · CeRP del Suroeste · Versión <?= htmlspecialchars($app_version) ?></p>
        </div>
    </div>
</footer>

<script>
(function () {
    var navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            navbar.classList.toggle('scrolled', window.scrollY > 40);
        }, { passive: true });
    }
    var h = document.getElementById('hamburger');
    var m = document.getElementById('mobileMenu');
    if (h && m) {
        h.addEventListener('click', function () {
            m.classList.toggle('open');
            h.classList.toggle('active');
        });
    }
})();
</script>
