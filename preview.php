<?php
/**
 * ZELIA 2.0 — Prototipo UI/UX
 * ⚠ Página de prueba independiente. No modifica nada de la web actual.
 * Cuando el cliente apruebe este diseño, se migra al resto.
 */

// Mock data — no necesita base de datos
$mockGames = [
    [
        'emoji'   => '🐍',
        'title'   => 'Snake Educativo',
        'desc'    => 'El clásico snake con un giro educativo. Controla la serpiente y aprende conceptos de lógica.',
        'badge'   => 'Lógica',
        'age'     => '12+',
        'rating'  => '4.0',
        'stars'   => '★★★★☆',
        'gradient'=> 'linear-gradient(135deg,#667eea,#764ba2)',
    ],
    [
        'emoji'   => '🔢',
        'title'   => 'Adivina el Número',
        'desc'    => 'Pon a prueba tu intuición matemática adivinando el número correcto con pistas.',
        'badge'   => 'Matemática',
        'age'     => '8+',
        'rating'  => '5.0',
        'stars'   => '★★★★★',
        'gradient'=> 'linear-gradient(135deg,#f093fb,#f5576c)',
    ],
    [
        'emoji'   => '🧠',
        'title'   => 'ASCII Memory',
        'desc'    => 'Juego de memoria con caracteres ASCII. Entrena tu concentración y velocidad mental.',
        'badge'   => 'Memoria',
        'age'     => '6+',
        'rating'  => '4.2',
        'stars'   => '★★★★☆',
        'gradient'=> 'linear-gradient(135deg,#4facfe,#00f2fe)',
    ],
    [
        'emoji'   => '🔑',
        'title'   => 'Código Perdido',
        'desc'    => 'Descifra el código oculto usando pistas lógicas y razonamiento deductivo.',
        'badge'   => 'Lógica',
        'age'     => '14+',
        'rating'  => '4.8',
        'stars'   => '★★★★★',
        'gradient'=> 'linear-gradient(135deg,#43e97b,#38f9d7)',
    ],
    [
        'emoji'   => '🌺',
        'title'   => 'Rosco de Palabras',
        'desc'    => 'El famoso Pasapalabra adaptado para practicar vocabulario y ortografía.',
        'badge'   => 'Lengua',
        'age'     => '10+',
        'rating'  => '4.9',
        'stars'   => '★★★★★',
        'gradient'=> 'linear-gradient(135deg,#fa709a,#fee140)',
    ],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZELIA 2.0 — Prototipo UI/UX</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- CSS NUEVO — independiente, no toca style.css -->
    <link rel="stylesheet" href="css/style-preview.css">
</head>
<body>

    <!-- ═══ BANNER DE PROTOTIPO ═══════════════════════════ -->
    <div class="proto-banner">
        🎨 &nbsp;<strong>PROTOTIPO UI/UX</strong> — Vista previa del nuevo diseño · No afecta la web actual
    </div>

    <!-- ═══ NAVBAR ════════════════════════════════════════ -->
    <header class="navbar" id="navbar">
        <div class="navbar-inner">
            <a href="preview.php" class="navbar-brand">
                <span class="brand-icon">⬡</span>
                <span class="brand-name">ZELIA</span>
                <span class="brand-tag">EDU</span>
            </a>

            <nav class="navbar-links">
                <a href="#inicio"   class="nav-link active">Inicio</a>
                <a href="#nosotros" class="nav-link">Nosotros</a>
                <a href="#juegos"   class="nav-link">Juegos</a>
                <a href="index.php" class="nav-link" title="Volver a la web actual" style="opacity:.5;font-size:12px;">← Web actual</a>
                <a href="#"         class="nav-link btn-login">Iniciar sesión</a>
            </nav>

            <button class="hamburger" id="hamburger" aria-label="Menú">
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>

    <!-- Mobile menu -->
    <div class="mobile-menu" id="mobileMenu">
        <a href="#inicio"   class="nav-link">Inicio</a>
        <a href="#nosotros" class="nav-link">Nosotros</a>
        <a href="#juegos"   class="nav-link">Juegos</a>
        <a href="index.php" class="nav-link" style="opacity:.5;font-size:13px;">← Web actual</a>
        <a href="#"         class="nav-link btn-login" style="text-align:center;margin-top:8px;">Iniciar sesión</a>
    </div>

    <!-- ═══ HERO ══════════════════════════════════════════ -->
    <section class="hero" id="inicio">
        <div class="hero-glow"></div>
        <div class="container">

            <!-- Texto -->
            <div class="hero-content">
                <div class="hero-badge">🎮 Plataforma Educativa Digital · 2026</div>
                <h1 class="hero-title">
                    Aprende<br>
                    <span class="gradient-text">jugando</span>
                </h1>
                <p class="hero-desc">
                    Videojuegos educativos diseñados para que estudiantes aprendan
                    de manera divertida e interactiva. Elige tu juego y comienza.
                </p>
                <div class="hero-actions">
                    <a href="#juegos" class="btn btn-primary">▶ &nbsp;Explorar juegos</a>
                    <a href="#nosotros" class="btn btn-ghost">Conocer el proyecto</a>
                </div>
                <div class="hero-stats">
                    <div class="stat">
                        <strong>5+</strong>
                        <span>Juegos</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat">
                        <strong>100%</strong>
                        <span>Gratuito</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat">
                        <strong>2026</strong>
                        <span>Versión</span>
                    </div>
                </div>
            </div>

            <!-- Visual decorativo -->
            <div class="hero-visual">
                <div class="floating-card fc1">
                    <span>🐍</span>
                    <p>SNAKE</p>
                </div>
                <div class="floating-card fc2">
                    <span>🔢</span>
                    <p>NÚMEROS</p>
                </div>
                <div class="floating-card fc3">
                    <span>🧠</span>
                    <p>MEMORIA</p>
                </div>
                <div class="hero-circle"></div>
            </div>
        </div>
    </section>

    <!-- ═══ SEARCH & FILTERS ══════════════════════════════ -->
    <section class="search-section" id="juegos">
        <div class="container">
            <div class="search-bar">
                <!-- Búsqueda -->
                <div class="search-input-wrap">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" placeholder="Buscar juegos..." class="search-input" id="searchInput">
                </div>

                <!-- Categorías -->
                <div class="filter-chips" id="chips">
                    <button class="chip chip--active" data-cat="">Todos</button>
                    <button class="chip" data-cat="Matemática">Matemática</button>
                    <button class="chip" data-cat="Lengua">Lengua</button>
                    <button class="chip" data-cat="Lógica">Lógica</button>
                    <button class="chip" data-cat="Memoria">Memoria</button>
                </div>

                <!-- Edad -->
                <div>
                    <select class="age-select" id="ageSelect">
                        <option value="">Todas las edades</option>
                        <option value="6+">6+</option>
                        <option value="8+">8+</option>
                        <option value="10+">10+</option>
                        <option value="12+">12+</option>
                        <option value="14+">14+</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ GAMES GRID ════════════════════════════════════ -->
    <section class="games-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Juegos disponibles</h2>
                <span class="section-count" id="countLabel"><?= count($mockGames) ?> juegos</span>
            </div>

            <div class="games-grid" id="gamesGrid">
                <?php foreach ($mockGames as $g): ?>
                <a href="#" class="game-card"
                   data-cat="<?= htmlspecialchars($g['badge']) ?>"
                   data-age="<?= htmlspecialchars($g['age']) ?>"
                   data-title="<?= strtolower(htmlspecialchars($g['title'])) ?> <?= strtolower(htmlspecialchars($g['desc'])) ?>">

                    <div class="game-card-img" style="background:<?= $g['gradient'] ?>">
                        <span class="game-emoji"><?= $g['emoji'] ?></span>
                        <div class="game-card-overlay">
                            <span class="play-btn">▶ Jugar</span>
                        </div>
                    </div>

                    <div class="game-card-body">
                        <div class="game-card-meta">
                            <span class="game-badge"><?= htmlspecialchars($g['badge']) ?></span>
                            <span class="game-age"><?= htmlspecialchars($g['age']) ?></span>
                        </div>
                        <h3 class="game-title"><?= htmlspecialchars($g['title']) ?></h3>
                        <p class="game-desc"><?= htmlspecialchars($g['desc']) ?></p>
                        <div class="game-footer">
                            <div class="stars"><?= $g['stars'] ?></div>
                            <span class="game-rating"><?= $g['rating'] ?></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══ ABOUT / NOSOTROS ══════════════════════════════ -->
    <section class="about-section" id="nosotros">
        <div class="container">
            <div class="about-grid">

                <!-- Texto -->
                <div class="about-text">
                    <span class="section-badge">Taller Integrador II · 2026</span>
                    <h2 class="about-title">
                        Tecnología al servicio<br>de la educación
                    </h2>
                    <p class="about-desc">
                        Somos un equipo del Profesorado de Informática comprometido con
                        el desarrollo de recursos educativos digitales innovadores que
                        promuevan el aprendizaje activo y el pensamiento computacional.
                    </p>
                    <a href="#" class="btn btn-outline">Conocer más &nbsp;→</a>
                </div>

                <!-- Feature cards -->
                <div class="about-cards">
                    <div class="feat-card">
                        <span class="feat-icon">🎓</span>
                        <h4>Pedagógico</h4>
                        <p>Diseñado con criterio educativo por docentes y estudiantes del profesorado.</p>
                    </div>
                    <div class="feat-card">
                        <span class="feat-icon">🎮</span>
                        <h4>Interactivo</h4>
                        <p>Aprende jugando con mecánicas pensadas para el aula y el hogar.</p>
                    </div>
                    <div class="feat-card">
                        <span class="feat-icon">🆓</span>
                        <h4>Gratuito</h4>
                        <p>Acceso libre y sin registro para todos los estudiantes y docentes.</p>
                    </div>
                    <div class="feat-card">
                        <span class="feat-icon">📱</span>
                        <h4>Responsive</h4>
                        <p>Funciona en celular, tablet y computadora sin instalar nada.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ EQUIPO ════════════════════════════════════════ -->
    <section class="team-section">
        <div class="container">
            <div class="team-section-header">
                <span class="section-badge">El equipo</span>
                <h2 class="team-section-title">Quiénes somos</h2>
                <p class="team-section-desc">
                    Un grupo de estudiantes y su docente trabajando juntos para crear
                    experiencias de aprendizaje únicas a través de los videojuegos.
                </p>
            </div>

            <div class="team-grid">
                <div class="team-card team-card--docente">
                    <span class="team-avatar">👨‍🏫</span>
                    <p class="team-name">Domingo Perez</p>
                    <p class="team-role">Docente</p>
                </div>
                <div class="team-card">
                    <span class="team-avatar">👨‍💻</span>
                    <p class="team-name">Yonhatan Pereira</p>
                    <p class="team-role">Estudiante</p>
                </div>
                <div class="team-card">
                    <span class="team-avatar">👩‍💻</span>
                    <p class="team-name">Lucía Rodríguez</p>
                    <p class="team-role">Estudiante</p>
                </div>
                <div class="team-card">
                    <span class="team-avatar">👨‍💻</span>
                    <p class="team-name">Emiliano Urruti</p>
                    <p class="team-role">Estudiante</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ FOOTER ════════════════════════════════════════ -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-inner">
                <div class="footer-brand">
                    <span class="brand-icon" style="font-size:22px;color:var(--primary);filter:drop-shadow(0 0 8px rgba(139,92,246,.5))">⬡</span>
                    <span style="font-family:'Poppins',sans-serif;font-weight:800;font-size:18px;letter-spacing:3px;background:linear-gradient(135deg,#A78BFA,#06B6D4);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">ZELIA</span>
                </div>
                <p class="footer-desc">Zona Educativa Lúdica con Inteligencia Artificial</p>
                <p class="footer-copy">© 2026 · Taller Integrador II · Profesorado de Informática</p>
            </div>
        </div>
    </footer>

    <!-- ═══ JS INTERACCIONES (solo demo) ════════════════ -->
    <script>
    (() => {
        // Navbar scroll
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 40);
        }, { passive: true });

        // Hamburger
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');
        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
            hamburger.classList.toggle('active');
        });

        // Chips filter demo
        const chips    = document.querySelectorAll('.chip');
        const cards    = document.querySelectorAll('.game-card');
        const search   = document.getElementById('searchInput');
        const ageSelect = document.getElementById('ageSelect');
        const countLabel = document.getElementById('countLabel');
        let activeCat  = '';
        let activeAge  = '';
        let searchVal  = '';

        function applyFilters() {
            let visible = 0;
            cards.forEach(card => {
                const cat   = card.dataset.cat   || '';
                const age   = card.dataset.age   || '';
                const title = card.dataset.title  || '';
                const matchCat  = !activeCat  || cat  === activeCat;
                const matchAge  = !activeAge  || age  === activeAge;
                const matchSrch = !searchVal  || title.includes(searchVal.toLowerCase());
                const show = matchCat && matchAge && matchSrch;
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            countLabel.textContent = visible + ' juego' + (visible !== 1 ? 's' : '');
        }

        chips.forEach(chip => {
            chip.addEventListener('click', () => {
                chips.forEach(c => c.classList.remove('chip--active'));
                chip.classList.add('chip--active');
                activeCat = chip.dataset.cat;
                applyFilters();
            });
        });

        search.addEventListener('input', () => {
            searchVal = search.value.trim();
            applyFilters();
        });

        ageSelect.addEventListener('change', () => {
            activeAge = ageSelect.value;
            applyFilters();
        });
    })();
    </script>
</body>
</html>
