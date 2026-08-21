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

        // Formata chips de colunas nos cards de projeto do Dashboard
        formatProjectStatChips();

        // Remove linhas de ícones vazias nos cards do board
        cleanEmptyIconRows();

        // Clique em qualquer ponto da coluna/cabeçalho recolhido para expandi-la
        document.addEventListener('click', function (e) {
            var collapsedTarget = e.target.closest('th.board-column-header-collapsed, td.board-column-task-collapsed');
            if (!collapsedTarget) return;

            // Se o clique já foi no próprio botão/link de toggle, deixa o evento seguir
            if (e.target.closest('.board-toggle-column-view')) return;

            var columnMatch = collapsedTarget.className.match(/board-column(?:-header)?-(\d+)/);
            var columnId = collapsedTarget.getAttribute('data-column-id') || (columnMatch ? columnMatch[1] : null);
            if (columnId) {
                var toggleLink = document.querySelector('td.board-column-' + columnId + ' .board-toggle-column-view, th.board-column-header-' + columnId + ' .board-toggle-column-view');
                if (toggleLink) {
                    toggleLink.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));
                }
            }
        });

        // Configurações do Tema (BoardRenewal Theme Settings)
        initSettingsPage();
    });

    // Remove nós de linhas de ícones que não contêm nenhum elemento filho nem texto
    function cleanEmptyIconRows() {
        var rows = document.querySelectorAll('.task-board-icons-row');
        rows.forEach(function (row) {
            if (!row.children.length && !row.textContent.trim()) {
                row.remove();
            }
        });
    }

    // Formata chips de colunas nos cards de projeto do Dashboard
    function formatProjectStatChips() {
        var containers = document.querySelectorAll('#dashboard .table-list-row .table-list-details, .table-list .table-list-details');
        containers.forEach(function (container) {
            if (container.getAttribute('data-formatted') === 'true') return;
            var smalls = container.querySelectorAll('small');
            if (smalls.length === 0) return;

            var chips = [];
            var nodes = Array.from(container.childNodes);
            var currentNumber = '';

            nodes.forEach(function (node) {
                if (node.nodeType === Node.TEXT_NODE) {
                    var text = node.textContent.trim();
                    if (text) currentNumber = text;
                } else if (node.nodeName === 'SMALL') {
                    var label = node.textContent.trim();
                    var count = parseInt(currentNumber, 10) || 0;
                    var chip = document.createElement('span');
                    chip.className = 'br-stat-chip' + (count > 0 ? ' br-stat-chip--active' : '');
                    chip.innerHTML = '<span class="br-stat-chip__count">' + (currentNumber || '0') + '</span><span class="br-stat-chip__label">' + label + '</span>';
                    chips.push(chip);
                    currentNumber = '';
                }
            });

            if (chips.length > 0) {
                container.innerHTML = '';
                chips.forEach(function (chip) { container.appendChild(chip); });
                container.setAttribute('data-formatted', 'true');
            }
        });
    }

    // ----------------------------------------------------
    // Configurações do Tema (BoardRenewal Theme Settings)
    // ----------------------------------------------------
    function initSettingsPage() {
        var settingsForm = document.querySelector('.br-settings-form');
        if (!settingsForm) return;

        // 1. Paleta de Cores
        var paletteCards = document.querySelectorAll('.br-palette-card');
        var paletteRadios = document.querySelectorAll('.br-palette-card__radio');
        var customContainer = document.getElementById('br-custom-color-container');
        var customSwatch = document.querySelector('.br-custom-primary-swatch');
        var customPicker = document.getElementById('br-custom-accent-picker');
        var customText = document.getElementById('boardrenewal_custom_accent');

        function updatePaletteSelection(selectedVal) {
            paletteCards.forEach(function (c) {
                var r = c.querySelector('.br-palette-card__radio');
                if (r && r.value === selectedVal) {
                    r.checked = true;
                    c.classList.add('br-palette-card--selected');
                } else {
                    c.classList.remove('br-palette-card--selected');
                }
            });

            if (customContainer) {
                customContainer.style.display = (selectedVal === 'custom') ? 'block' : 'none';
            }
        }

        function selectCustomPalette() {
            updatePaletteSelection('custom');
        }

        function updateCustomAccent(val) {
            if (!val) return;
            var cleanHex = val.trim();
            if (cleanHex.charAt(0) !== '#') cleanHex = '#' + cleanHex;

            if (/^#[0-9a-fA-F]{6}$/.test(cleanHex)) {
                if (customPicker) customPicker.value = cleanHex;
                if (customSwatch) customSwatch.style.backgroundColor = cleanHex;
            }
            if (customText) {
                customText.value = cleanHex;
            }
        }

        paletteCards.forEach(function (card) {
            card.addEventListener('click', function () {
                var radio = this.querySelector('.br-palette-card__radio');
                if (radio) {
                    updatePaletteSelection(radio.value);
                }
            });
        });

        paletteRadios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                if (this.checked) {
                    updatePaletteSelection(this.value);
                }
            });
        });

        if (customPicker && customText) {
            var handleCustomInput = function () {
                customText.value = customPicker.value;
                if (customSwatch) customSwatch.style.backgroundColor = customPicker.value;
                selectCustomPalette();
            };
            customPicker.addEventListener('input', handleCustomInput);
            customPicker.addEventListener('change', handleCustomInput);

            customText.addEventListener('input', function () {
                var v = this.value.trim();
                if (v.charAt(0) !== '#') v = '#' + v;
                if (/^#[0-9a-fA-F]{6}$/.test(v)) {
                    customPicker.value = v;
                    if (customSwatch) customSwatch.style.backgroundColor = v;
                }
                selectCustomPalette();
            });
        }

        // Chips de cores sugeridas
        var accentChips = document.querySelectorAll('.br-accent-chip-btn');
        accentChips.forEach(function (chip) {
            chip.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var color = this.getAttribute('data-color');
                if (color) {
                    updateCustomAccent(color);
                    selectCustomPalette();
                }
            });
        });

        // 2. Fundo dos Cards (Claro e Escuro)
        var cardLightPicker = document.getElementById('br-card-bg-light-picker');
        var cardLightText = document.getElementById('boardrenewal_card_bg_light');
        var cardDarkPicker = document.getElementById('br-card-bg-dark-picker');
        var cardDarkText = document.getElementById('boardrenewal_card_bg_dark');
        var previewCardLight = document.getElementById('br-card-preview-light');
        var previewCardDark = document.getElementById('br-card-preview-dark');
        var resetLightBtn = document.getElementById('br-reset-card-light');
        var resetDarkBtn = document.getElementById('br-reset-card-dark');

        function updateCardPreviews() {
            var lightVal = cardLightText ? cardLightText.value.trim() : '';
            var darkVal = cardDarkText ? cardDarkText.value.trim() : '';

            if (previewCardLight) {
                previewCardLight.style.backgroundColor = lightVal || '#ffffff';
            }
            if (previewCardDark) {
                previewCardDark.style.backgroundColor = darkVal || '#1e2030';
            }
        }

        if (cardLightPicker && cardLightText) {
            var handleLightInput = function () {
                cardLightText.value = cardLightPicker.value;
                updateCardPreviews();
            };
            cardLightPicker.addEventListener('input', handleLightInput);
            cardLightPicker.addEventListener('change', handleLightInput);

            cardLightText.addEventListener('input', function () {
                var v = this.value.trim();
                if (v.charAt(0) !== '#') v = '#' + v;
                if (/^#[0-9a-fA-F]{6}$/.test(v)) {
                    cardLightPicker.value = v;
                }
                updateCardPreviews();
            });
        }

        if (cardDarkPicker && cardDarkText) {
            var handleDarkInput = function () {
                cardDarkText.value = cardDarkPicker.value;
                updateCardPreviews();
            };
            cardDarkPicker.addEventListener('input', handleDarkInput);
            cardDarkPicker.addEventListener('change', handleDarkInput);

            cardDarkText.addEventListener('input', function () {
                var v = this.value.trim();
                if (v.charAt(0) !== '#') v = '#' + v;
                if (/^#[0-9a-fA-F]{6}$/.test(v)) {
                    cardDarkPicker.value = v;
                }
                updateCardPreviews();
            });
        }

        if (resetLightBtn && cardLightText && cardLightPicker) {
            resetLightBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                cardLightText.value = '';
                cardLightPicker.value = '#ffffff';
                updateCardPreviews();
            });
        }

        if (resetDarkBtn && cardDarkText && cardDarkPicker) {
            resetDarkBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                cardDarkText.value = '';
                cardDarkPicker.value = '#1e2030';
                updateCardPreviews();
            });
        }

        // Presets rápidos de fundo de card
        var presetButtons = document.querySelectorAll('.br-card-preset-btn');
        presetButtons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var light = this.getAttribute('data-light');
                var dark = this.getAttribute('data-dark');
                if (light && cardLightText && cardLightPicker) {
                    cardLightText.value = light;
                    cardLightPicker.value = light;
                }
                if (dark && cardDarkText && cardDarkPicker) {
                    cardDarkText.value = dark;
                    cardDarkPicker.value = dark;
                }
                updateCardPreviews();
            });
        });

        // 3. Texturas / Imagem
        var textureSelect = document.getElementById('boardrenewal_bg_texture');
        var bgGroup = document.getElementById('br-bg-image-group');
        var opacityRange = document.getElementById('boardrenewal_bg_opacity_range');
        var opacityText = document.getElementById('boardrenewal_bg_opacity');

        if (textureSelect && bgGroup) {
            textureSelect.addEventListener('change', function () {
                bgGroup.style.display = (this.value === 'custom_image') ? 'block' : 'none';
            });
        }

        if (opacityRange && opacityText) {
            opacityRange.addEventListener('input', function () { opacityText.value = this.value; });
            opacityText.addEventListener('input', function () { opacityRange.value = this.value; });
        }

        // 4. Prévia de Logo e Nome
        var logoInput = document.getElementById('boardrenewal_logo_url');
        var brandInput = document.getElementById('boardrenewal_brand_name');
        var previewImg = document.getElementById('br-preview-logo-img');
        var previewDefaultIcon = document.getElementById('br-preview-default-icon');
        var previewText = document.getElementById('br-preview-brand-text');

        function updateBrandPreview() {
            var url = logoInput ? logoInput.value.trim() : '';
            var name = brandInput ? brandInput.value.trim() : '';

            if (url && previewImg && previewDefaultIcon) {
                previewImg.src = url;
                previewImg.style.display = 'inline-block';
                previewDefaultIcon.style.display = 'none';
            } else if (previewImg && previewDefaultIcon) {
                previewImg.style.display = 'none';
                previewDefaultIcon.style.display = 'inline-block';
            }

            if (previewText) {
                previewText.textContent = name || 'Kanboard';
            }
        }

        if (logoInput) logoInput.addEventListener('input', updateBrandPreview);
        if (brandInput) brandInput.addEventListener('input', updateBrandPreview);
    }

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
