(function () {
    'use strict';

    var root = document.documentElement;

    // Restaura o estado da sidebar do localStorage
    var savedSidebar = null;
    try { savedSidebar = localStorage.getItem('boardrenewal.sidebar'); } catch (e) {}
    if (savedSidebar === 'collapsed') {
        root.setAttribute('data-sidebar', 'collapsed');
    } else {
        root.removeAttribute('data-sidebar');
    }

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

        // Fecha o modal ao clicar no fundo escuro (fora do #modal-box)
        document.addEventListener('click', function (e) {
            var overlay = document.getElementById('modal-overlay');
            if (overlay && e.target === overlay) {
                var closeBtn = document.getElementById('modal-close-button');
                if (closeBtn) {
                    closeBtn.click();
                } else if (window.KB && window.KB.modal && typeof window.KB.modal.close === 'function') {
                    window.KB.modal.close();
                }
            }
        });

        // Feed de atividades: adiciona ícone da ação e transforma o autor em link
        enhanceActivityFeed();
    });

    // Reestrutura o feed de atividades: cada card ganha um ícone de ação
    // (detectado por palavras-chave) e o nome do autor vira link de perfil.
    function enhanceActivityFeed() {
        var events = document.querySelectorAll('.activity-event');
        events.forEach(function (event) {
            var title = event.querySelector('.activity-title');
            if (!title || title.querySelector('.activity-icon')) return; // já processado

            var text = (title.textContent || '').toLowerCase();

            // Ícone da ação baseado no verbo da atividade
            var icon = 'fa-bolt';
            if (text.indexOf('criou') > -1) icon = 'fa-plus';
            else if (text.indexOf('moveu') > -1) icon = 'fa-arrows';
            else if (text.indexOf('atualizou') > -1) icon = 'fa-pencil';
            else if (text.indexOf('removeu') > -1 || text.indexOf('excluiu') > -1) icon = 'fa-trash';
            else if (text.indexOf('coment') > -1) icon = 'fa-comment';
            else if (text.indexOf('finalizou') > -1 || text.indexOf('fechou') > -1) icon = 'fa-check';
            else if (text.indexOf('anex') > -1 || text.indexOf('arquivo') > -1) icon = 'fa-paperclip';

            var iconEl = document.createElement('i');
            iconEl.className = 'fa ' + icon + ' activity-icon';
            iconEl.setAttribute('aria-hidden', 'true');

            // Autor: primeiro nó de texto do título vira link de perfil
            // (processa ANTES de inserir o ícone, pois o ícone é um elemento)
            var authorLink = wrapAuthorInLink(title);

            title.insertBefore(iconEl, title.firstChild);
            if (authorLink) {
                // move o link do autor para logo após o ícone
                iconEl.after(authorLink);
            }
        });
    }

    // Envelopa o nome do autor (texto solto no início do título) em <a class="activity-author">
    function wrapAuthorInLink(title) {
        // O autor é o primeiro nó de texto antes do primeiro <a> (link da tarefa)
        var firstText = null;
        for (var i = 0; i < title.childNodes.length; i++) {
            var node = title.childNodes[i];
            if (node.nodeType === Node.TEXT_NODE && node.textContent.trim().length > 0) {
                firstText = node;
                break;
            }
            if (node.nodeType === Node.ELEMENT_NODE) break; // já começa com elemento
        }
        if (!firstText) return null;

        // O texto é algo como "admin atualizou uma subtarefa da tarefa ";
        // o autor é a primeira palavra
        var raw = firstText.textContent;
        var match = raw.match(/^\s*(\S+)\s/);
        if (!match) return null;
        var author = match[1];

        var a = document.createElement('a');
        a.className = 'activity-author';
        a.href = '#';
        a.textContent = author;
        a.title = author;

        // substitui o nome do autor no texto original por vazio
        firstText.textContent = raw.replace(author, '');
        return a;
    }
})();
