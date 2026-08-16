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
        // Move o seletor de projetos da topbar para a sidebar
        var boardSelector = document.querySelector('.board-selector-container');
        var sidebarSelector = document.querySelector('.br-sidebar__project-selector');
        if (boardSelector && sidebarSelector) {
            sidebarSelector.appendChild(boardSelector);
            boardSelector.style.display = 'block';
        }

        // Adiciona avatar no cabeçalho do dropdown do usuário
        // (capture: o app.min.js faz stopPropagation no clique do dropdown)
        document.addEventListener('click', function (e) {
            var userDropdown = e.target.closest('.dropdown');
            if (!userDropdown) return;
            // O Kanboard troca .dropdown-menu por .active-dropdown-menu ao abrir,
            // então busca qualquer âncora e confirma pela presença do avatar
            var anchor = userDropdown.querySelector('a');
            if (!anchor || !anchor.querySelector('.avatar')) return;
            // Aguarda o Kanboard clonar o dropdown
            setTimeout(function () {
                var cloned = document.querySelector('#dropdown ul.dropdown-submenu-open');
                if (!cloned) return;
                var strong = cloned.querySelector('li strong');
                if (strong && !strong.getAttribute('data-initial')) {
                    var initial = (strong.textContent || '?').trim().charAt(0).toUpperCase();
                    strong.setAttribute('data-initial', initial);
                }
            }, 50);
        }, true);

        var contrastButton = document.getElementById('br-contrast-toggle');
        if (contrastButton) {
            syncContrastButton(contrastButton);
            contrastButton.addEventListener('click', function () {
                toggleContrast(contrastButton);
            });
        }

        // Botão de busca da sidebar: alterna a visibilidade do filtro de tarefas
        var searchButton = document.getElementById('br-search-button');
        if (searchButton) {
            searchButton.addEventListener('click', function () {
                var filterBox = document.querySelector('.filter-box-component');
                if (filterBox) {
                    filterBox.classList.toggle('br-filter-visible');
                    if (filterBox.classList.contains('br-filter-visible')) {
                        var input = filterBox.querySelector('input[type="text"], input[type="search"]');
                        if (input) input.focus();
                    }
                }
            });
        }

        // Recolher/expandar a sidebar (persistido; estado inicial aplicado no <html>
        // por script inline no head, antes do paint)
        var collapseButton = document.getElementById('br-sidebar-collapse');
        if (collapseButton) {
            collapseButton.textContent =
                root.getAttribute('data-sidebar') === 'collapsed' ? '▶' : '◀';
            collapseButton.addEventListener('click', function () {
                var collapsed = root.getAttribute('data-sidebar') === 'collapsed';
                if (collapsed) {
                    root.removeAttribute('data-sidebar');
                } else {
                    root.setAttribute('data-sidebar', 'collapsed');
                }
                collapseButton.textContent = collapsed ? '◀' : '▶';
                try { localStorage.setItem('boardrenewal.sidebar', collapsed ? 'expanded' : 'collapsed'); } catch (e) {}
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
