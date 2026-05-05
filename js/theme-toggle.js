(function () {
    var STORAGE_KEY = 'zelia-theme';
    var LIGHT_VALUE = 'light';
    var LEGACY_CONTRAST_KEY = 'zelia-contrast';

    function getSavedTheme() {
        var savedTheme = localStorage.getItem(STORAGE_KEY);
        if (savedTheme === LIGHT_VALUE || savedTheme === 'dark') {
            return savedTheme;
        }

        // Compatibilidad con la version anterior de "high contrast".
        var legacy = localStorage.getItem(LEGACY_CONTRAST_KEY);
        return legacy === 'high' ? LIGHT_VALUE : 'dark';
    }

    function applyTheme(theme) {
        var isLight = theme === LIGHT_VALUE;
        document.body.classList.toggle('theme-light', isLight);
        document.body.dataset.theme = isLight ? LIGHT_VALUE : 'dark';
    }

    function ensureToggleButton() {
        var existing = document.getElementById('contrast-toggle');
        if (existing) {
            return existing;
        }

        var button = document.createElement('button');
        button.id = 'contrast-toggle';
        button.type = 'button';
        button.title = 'Cambiar tema claro/oscuro';
        button.setAttribute('aria-label', 'Cambiar tema claro u oscuro');
        document.body.appendChild(button);
        return button;
    }

    function updateButtonState(button, theme) {
        var isLight = theme === LIGHT_VALUE;
        button.textContent = isLight ? '☀' : '☾';
        button.title = isLight ? 'Cambiar a tema oscuro' : 'Cambiar a tema claro';
        button.setAttribute('aria-label', button.title);
    }

    function setupThemeToggle() {
        var currentTheme = getSavedTheme();
        applyTheme(currentTheme);

        var toggleButton = ensureToggleButton();
        updateButtonState(toggleButton, currentTheme);

        toggleButton.addEventListener('click', function () {
            var nextTheme = document.body.classList.contains('theme-light') ? 'dark' : LIGHT_VALUE;
            applyTheme(nextTheme);
            localStorage.setItem(STORAGE_KEY, nextTheme);
            updateButtonState(toggleButton, nextTheme);

            toggleButton.style.transform = 'scale(0.92)';
            setTimeout(function () {
                toggleButton.style.transform = '';
            }, 100);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupThemeToggle);
    } else {
        setupThemeToggle();
    }
})();
