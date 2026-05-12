<footer class="site-footer">
    <div class="container">
        <div class="footer-inner">
            <div class="footer-brand">
                <span style="font-size:22px;color:var(--primary);filter:drop-shadow(0 0 8px rgba(139,92,246,0.5))">⬡</span>
                <span class="footer-brand-name">ZELIA</span>
            </div>
            <p class="footer-desc">Zona Educativa Lúdica con Inteligencia Artificial</p>
            <p class="footer-copy">© 2026 · Taller Integrador II · Profesorado de Informática · CeRP del Suroeste</p>
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
