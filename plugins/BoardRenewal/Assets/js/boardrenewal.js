(function () {
    'use strict';

    var root = document.documentElement;

    // Alto contraste: persistido em localStorage até a Fase 3 (metadado de usuário)
    var savedContrast = null;
    try { savedContrast = localStorage.getItem('boardrenewal.contrast'); } catch (e) {}
    if (savedContrast === 'high') {
        root.setAttribute('data-contrast', 'high');
    }

    function syncContrastButton(button) {
        if (button) {
            button.setAttribute('aria-pressed',
                root.getAttribute('data-contrast') === 'high' ? 'true' : 'false');
        }
    }

    function toggleContrast(button) {
        var current = root.getAttribute('data-contrast') === 'high' ? 'default' : 'high';
        root.setAttribute('data-contrast', current);
        syncContrastButton(button);
        try { localStorage.setItem('boardrenewal.contrast', current); } catch (e) {}
    }

    document.addEventListener('DOMContentLoaded', function () {
        var contrastButton = document.getElementById('br-contrast-toggle');
        if (contrastButton) {
            syncContrastButton(contrastButton);
            contrastButton.addEventListener('click', function () {
                toggleContrast(contrastButton);
            });
        }

        // Botão de busca da sidebar: foca o campo de busca/pesquisa da página
        var searchButton = document.getElementById('br-search-button');
        if (searchButton) {
            searchButton.addEventListener('click', function () {
                var input = document.querySelector('section.page input[type="text"], section.page input[type="search"]');
                if (input) {
                    input.focus();
                    input.scrollIntoView({ block: 'center' });
                }
            });
        }

        // Drawer mobile
        var menuButton = document.getElementById('br-menu-button');
        var sidebar = document.getElementById('br-sidebar');
        if (menuButton && sidebar) {
            menuButton.addEventListener('click', function () {
                sidebar.classList.toggle('br-sidebar--open');
                document.body.classList.toggle('br-drawer-open');
            });
            document.addEventListener('click', function (e) {
                if (document.body.classList.contains('br-drawer-open') &&
                    !sidebar.contains(e.target) && !menuButton.contains(e.target)) {
                    sidebar.classList.remove('br-sidebar--open');
                    document.body.classList.remove('br-drawer-open');
                }
            });
        }
    });
})();
