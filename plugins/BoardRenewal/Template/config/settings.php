<div class="page-header">
    <h2><?= t('BoardRenewal Theme') ?> &mdash; <?= t('Customization') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('BoardRenewalConfigController', 'save', array('plugin' => 'BoardRenewal')) ?>" autocomplete="off" class="br-settings-form">
    <?= $this->form->csrf() ?>

    <?php
    $currentPalette = isset($values['boardrenewal_palette']) ? $values['boardrenewal_palette'] : 'default';
    $customAccent = isset($values['boardrenewal_custom_accent']) ? $values['boardrenewal_custom_accent'] : '#6366f1';
    $cardBgLight = isset($values['boardrenewal_card_bg_light']) ? $values['boardrenewal_card_bg_light'] : '';
    $cardBgDark = isset($values['boardrenewal_card_bg_dark']) ? $values['boardrenewal_card_bg_dark'] : '';
    $currentTexture = isset($values['boardrenewal_bg_texture']) ? $values['boardrenewal_bg_texture'] : 'none';
    $bgImageUrl = isset($values['boardrenewal_bg_image_url']) ? $values['boardrenewal_bg_image_url'] : '';
    $bgOpacity = isset($values['boardrenewal_bg_opacity']) ? $values['boardrenewal_bg_opacity'] : '0.08';
    $logoUrl = isset($values['boardrenewal_logo_url']) ? $values['boardrenewal_logo_url'] : '';
    $brandName = isset($values['boardrenewal_brand_name']) ? $values['boardrenewal_brand_name'] : '';
    ?>

    <!-- SEÇÃO 1: PALETA DE CORES -->
    <div class="br-settings-section">
        <div class="br-settings-section__header">
            <h3>🎨 <?= t('Color Palette') ?></h3>
            <p class="form-help"><?= t('Escolha o esquema de cores para os elementos de destaque, botões e navegação.') ?></p>
        </div>

        <div class="br-palette-grid">
            <?php foreach ($palettes as $key => $palette): ?>
                <label class="br-palette-card <?= $currentPalette === $key ? 'br-palette-card--selected' : '' ?>" data-palette-key="<?= $key ?>">
                    <input type="radio" name="boardrenewal_palette" value="<?= $key ?>" <?= $currentPalette === $key ? 'checked' : '' ?> class="br-palette-card__radio">
                    <div class="br-palette-card__content">
                        <div class="br-palette-card__title">
                            <strong><?= $this->text->e($palette['name']) ?></strong>
                        </div>
                        <?php if (!empty($palette['description'])): ?>
                            <div class="br-palette-card__desc"><?= $this->text->e($palette['description']) ?></div>
                        <?php endif ?>
                        <div class="br-palette-card__colors">
                            <?php if (isset($palette['preview'])): ?>
                                <?php foreach ($palette['preview'] as $colIndex => $col): ?>
                                    <span class="br-palette-card__swatch <?= ($key === 'custom' && $colIndex === 0) ? 'br-custom-primary-swatch' : '' ?>" style="background-color: <?= $col ?>;" title="<?= $col ?>"></span>
                                <?php endforeach ?>
                            <?php endif ?>
                        </div>
                    </div>
                </label>
            <?php endforeach ?>
        </div>

        <!-- Seletor de cor personalizada (ativo se custom for escolhido) -->
        <div class="br-custom-color-picker" id="br-custom-color-container" style="<?= $currentPalette === 'custom' ? '' : 'display: none;' ?>">
            <label for="boardrenewal_custom_accent"><strong><?= t('Cor de Destaque Personalizada (Hexadecimal):') ?></strong></label>
            <div class="br-color-input-group" style="margin-bottom: 10px;">
                <input type="color" id="br-custom-accent-picker" value="<?= $this->text->e($customAccent) ?>" class="br-color-picker-input">
                <input type="text" name="boardrenewal_custom_accent" id="boardrenewal_custom_accent" value="<?= $this->text->e($customAccent) ?>" placeholder="#6366f1" class="br-color-text-input">
            </div>
            <!-- Sugestões de Cores Rápidas -->
            <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-bottom: 8px;">
                <span style="font-size: 12px; color: var(--br-text-secondary); margin-right: 4px;"><?= t('Cores sugeridas:') ?></span>
                <button type="button" class="br-accent-chip-btn" data-color="#8b5cf6" style="background: #8b5cf6;" title="Roxo Violeta (#8b5cf6)"></button>
                <button type="button" class="br-accent-chip-btn" data-color="#6366f1" style="background: #6366f1;" title="Indigo (#6366f1)"></button>
                <button type="button" class="br-accent-chip-btn" data-color="#3b82f6" style="background: #3b82f6;" title="Azul Royal (#3b82f6)"></button>
                <button type="button" class="br-accent-chip-btn" data-color="#06b6d4" style="background: #06b6d4;" title="Ciano (#06b6d4)"></button>
                <button type="button" class="br-accent-chip-btn" data-color="#10b981" style="background: #10b981;" title="Esmeralda (#10b981)"></button>
                <button type="button" class="br-accent-chip-btn" data-color="#84cc16" style="background: #84cc16;" title="Lima (#84cc16)"></button>
                <button type="button" class="br-accent-chip-btn" data-color="#f59e0b" style="background: #f59e0b;" title="Âmbar (#f59e0b)"></button>
                <button type="button" class="br-accent-chip-btn" data-color="#f97316" style="background: #f97316;" title="Laranja (#f97316)"></button>
                <button type="button" class="br-accent-chip-btn" data-color="#ef4444" style="background: #ef4444;" title="Vermelho (#ef4444)"></button>
                <button type="button" class="br-accent-chip-btn" data-color="#ec4899" style="background: #ec4899;" title="Rosa Pink (#ec4899)"></button>
                <button type="button" class="br-accent-chip-btn" data-color="#d946ef" style="background: #d946ef;" title="Fúcsia (#d946ef)"></button>
            </div>
            <p class="form-help" style="margin: 0;"><?= t('Defina a cor primária de destaque. O tema gerará automaticamente variações de hover, fundos suaves e gradientes correspondentes.') ?></p>
        </div>
    </div>

    <!-- SEÇÃO 2: COR DE FUNDO DOS CARDS -->
    <div class="br-settings-section">
        <div class="br-settings-section__header">
            <h3>🃏 <?= t('Fundo dos Cards de Tarefas') ?></h3>
            <p class="form-help"><?= t('Personalize a cor de fundo dos cartões de tarefas no quadro para os modos Claro e Escuro.') ?></p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
            <!-- Modo Claro -->
            <div style="background: var(--br-surface-2); border: 1px solid var(--br-border); border-radius: var(--br-radius); padding: 16px;">
                <label for="boardrenewal_card_bg_light"><strong>☀️ <?= t('Modo Claro (Light Mode):') ?></strong></label>
                <div class="br-color-input-group" style="display: flex; align-items: center; gap: 8px; margin: 8px 0 10px;">
                    <input type="color" id="br-card-bg-light-picker" value="<?= !empty($cardBgLight) ? $this->text->e($cardBgLight) : '#ffffff' ?>" class="br-color-picker-input">
                    <input type="text" name="boardrenewal_card_bg_light" id="boardrenewal_card_bg_light" value="<?= $this->text->e($cardBgLight) ?>" placeholder="#ffffff (Padrão)" class="br-color-text-input">
                    <button type="button" class="btn btn-sm" id="br-reset-card-light" title="Restaurar padrão">↺</button>
                </div>
                <p class="form-help" style="margin: 0; font-size: 12px;"><?= t('Deixe em branco para usar o fundo padrão (#ffffff).') ?></p>
            </div>

            <!-- Modo Escuro -->
            <div style="background: var(--br-surface-2); border: 1px solid var(--br-border); border-radius: var(--br-radius); padding: 16px;">
                <label for="boardrenewal_card_bg_dark"><strong>🌙 <?= t('Modo Escuro (Dark Mode):') ?></strong></label>
                <div class="br-color-input-group" style="display: flex; align-items: center; gap: 8px; margin: 8px 0 10px;">
                    <input type="color" id="br-card-bg-dark-picker" value="<?= !empty($cardBgDark) ? $this->text->e($cardBgDark) : '#1e2030' ?>" class="br-color-picker-input">
                    <input type="text" name="boardrenewal_card_bg_dark" id="boardrenewal_card_bg_dark" value="<?= $this->text->e($cardBgDark) ?>" placeholder="#1e2030 (Padrão)" class="br-color-text-input">
                    <button type="button" class="btn btn-sm" id="br-reset-card-dark" title="Restaurar padrão">↺</button>
                </div>
                <p class="form-help" style="margin: 0; font-size: 12px;"><?= t('Deixe em branco para usar o fundo padrão (#1e2030).') ?></p>
            </div>
        </div>

        <!-- Presets Rápidos de Fundo de Card -->
        <div style="margin-top: 16px;">
            <label><strong>⚡ <?= t('Sugestões Rápidas:') ?></strong></label>
            <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 6px;">
                <button type="button" class="btn btn-sm br-card-preset-btn" data-light="#ffffff" data-dark="#1e2030">⚪ Padrão Neutro</button>
                <button type="button" class="btn btn-sm br-card-preset-btn" data-light="#f3faf6" data-dark="#16291f">🌲 Verde / Esmeralda Suave</button>
                <button type="button" class="btn btn-sm br-card-preset-btn" data-light="#f0f7ff" data-dark="#142336">🌊 Azul Oceano Suave</button>
                <button type="button" class="btn btn-sm br-card-preset-btn" data-light="#fdf4f5" data-dark="#29141c">🌹 Carmim / Rosé Suave</button>
                <button type="button" class="btn btn-sm br-card-preset-btn" data-light="#ffffff" data-dark="#111218">🌑 Noturno Profundo (OLED)</button>
            </div>
        </div>

        <!-- Prévia do Card ao Vivo -->
        <div style="margin-top: 20px; padding: 16px; background: var(--br-surface-2); border: 1px solid var(--br-border); border-radius: var(--br-radius);">
            <strong><?= t('Prévia em Tempo Real:') ?></strong>
            <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-top: 12px;">
                <!-- Prévia Modo Claro -->
                <div style="flex: 1; min-width: 240px; background: #eef1f6; padding: 12px; border-radius: var(--br-radius);">
                    <span style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">☀️ Modo Claro</span>
                    <div id="br-card-preview-light" style="background: <?= !empty($cardBgLight) ? $this->text->e($cardBgLight) : '#ffffff' ?>; border: 1px solid #e5e7ef; border-left: 3px solid #008833; border-radius: 6px; padding: 12px; margin-top: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.06); color: #0f172a;">
                        <div style="font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 4px;">#101</div>
                        <div style="font-weight: 600; font-size: 13.5px; margin-bottom: 8px;">Desenvolvimento de Recurso</div>
                        <div style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; background: rgba(0,0,0,0.04); border-radius: 4px; font-size: 11px; color: #475569;">
                            <span>✓ 100% (3/3)</span>
                            <span style="margin-left: 4px; font-weight: 700; color: #008833;">P0</span>
                        </div>
                    </div>
                </div>

                <!-- Prévia Modo Escuro -->
                <div style="flex: 1; min-width: 240px; background: #13141f; padding: 12px; border-radius: var(--br-radius);">
                    <span style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">🌙 Modo Escuro</span>
                    <div id="br-card-preview-dark" style="background: <?= !empty($cardBgDark) ? $this->text->e($cardBgDark) : '#1e2030' ?>; border: 1px solid #2f3249; border-left: 3px solid #10b981; border-radius: 6px; padding: 12px; margin-top: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.2); color: #f1f5f9;">
                        <div style="font-size: 11px; font-weight: 700; color: #94a3b8; margin-bottom: 4px;">#101</div>
                        <div style="font-weight: 600; font-size: 13.5px; margin-bottom: 8px;">Desenvolvimento de Recurso</div>
                        <div style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; background: rgba(255,255,255,0.06); border-radius: 4px; font-size: 11px; color: #94a3b8;">
                            <span>✓ 100% (3/3)</span>
                            <span style="margin-left: 4px; font-weight: 700; color: #10b981;">P0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEÇÃO 2: PLANO DE FUNDO & TEXTURAS -->
    <div class="br-settings-section">
        <div class="br-settings-section__header">
            <h3>🖼️ <?= t('Background & Textures') ?></h3>
            <p class="form-help"><?= t('Adicione texturas geométricas ou uma imagem personalizada ao fundo da aplicação.') ?></p>
        </div>

        <div class="br-form-group">
            <label for="boardrenewal_bg_texture"><strong><?= t('Padrão de Fundo:') ?></strong></label>
            <select name="boardrenewal_bg_texture" id="boardrenewal_bg_texture" class="br-select">
                <?php foreach ($textures as $tKey => $tex): ?>
                    <option value="<?= $tKey ?>" <?= $currentTexture === $tKey ? 'selected' : '' ?>>
                        <?= $this->text->e($tex['name']) ?> &mdash; <?= $this->text->e($tex['description']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="br-form-group" id="br-bg-image-group" style="<?= $currentTexture === 'custom_image' ? '' : 'display: none;' ?>">
            <label for="boardrenewal_bg_image_url"><strong><?= t('URL da Imagem de Fundo:') ?></strong></label>
            <input type="url" name="boardrenewal_bg_image_url" id="boardrenewal_bg_image_url" value="<?= $this->text->e($bgImageUrl) ?>" placeholder="https://exemplo.com/fundo.jpg" class="br-input-full">
            <p class="form-help"><?= t('Insira o endereço completo (HTTPS) de uma imagem ou textura.') ?></p>

            <label for="boardrenewal_bg_opacity" style="margin-top: 10px;"><strong><?= t('Opacidade da Imagem (0.01 a 0.50):') ?></strong></label>
            <div style="display: flex; align-items: center; gap: 12px;">
                <input type="range" id="boardrenewal_bg_opacity_range" min="0.01" max="0.50" step="0.01" value="<?= $this->text->e($bgOpacity) ?>" style="flex: 1; max-width: 250px;">
                <input type="text" name="boardrenewal_bg_opacity" id="boardrenewal_bg_opacity" value="<?= $this->text->e($bgOpacity) ?>" style="width: 70px; text-align: center;">
            </div>
            <p class="form-help"><?= t('Recomendado: 0.05 a 0.12 para manter excelente legibilidade dos quadros.') ?></p>
        </div>
    </div>

    <!-- SEÇÃO 3: LOGOTIPO E MARCA -->
    <div class="br-settings-section">
        <div class="br-settings-section__header">
            <h3>🏢 <?= t('Logo & Brand Identity') ?></h3>
            <p class="form-help"><?= t('Substitua o logotipo e a identificação visual no cabeçalho da barra lateral e na tela de login.') ?></p>
        </div>

        <div class="br-form-group">
            <label for="boardrenewal_brand_name"><strong><?= t('Nome da Instituição / Aplicação:') ?></strong></label>
            <input type="text" name="boardrenewal_brand_name" id="boardrenewal_brand_name" value="<?= $this->text->e($brandName) ?>" placeholder="Kanboard" class="br-input-full">
            <p class="form-help"><?= t('Texto exibido ao lado do logo na barra lateral e no título do login (padrão: "Kanboard").') ?></p>
        </div>

        <div class="br-form-group">
            <label for="boardrenewal_logo_url"><strong><?= t('URL do Logotipo / Ícone:') ?></strong></label>
            <input type="url" name="boardrenewal_logo_url" id="boardrenewal_logo_url" value="<?= $this->text->e($logoUrl) ?>" placeholder="https://exemplo.com/logo.svg" class="br-input-full">
            <div class="form-help br-help-box">
                <p>💡 <strong>Especificações recomendadas para o Logotipo:</strong></p>
                <ul>
                    <li><strong>Formato:</strong> SVG vetorial ou PNG com fundo transparente.</li>
                    <li><strong>Dimensões ideais:</strong> Proporção quadrada (1:1 ~32x32px) para exibição ideal com menu recolhido, ou horizontal (altura de 28px a 36px, largura de até 180px) com menu expandido.</li>
                    <li><strong>Aplicação:</strong> Este logotipo será exibido automaticamente no topo da barra lateral e na página de autenticação/login.</li>
                </ul>
            </div>
        </div>

        <!-- Prévia do Logotipo -->
        <div class="br-logo-preview-box" id="br-logo-preview-container">
            <strong><?= t('Prévia da Identidade Visual:') ?></strong>
            <div class="br-logo-preview-display">
                <div class="br-preview-brand">
                    <img id="br-preview-logo-img" src="<?= !empty($logoUrl) ? $this->text->e($logoUrl) : '' ?>" alt="Logo" style="<?= !empty($logoUrl) ? '' : 'display: none;' ?> max-height: 28px; max-width: 160px; object-fit: contain;">
                    <svg id="br-preview-default-icon" class="br-icon br-icon--lg" viewBox="0 0 24 24" style="<?= !empty($logoUrl) ? 'display: none;' : '' ?>" aria-hidden="true">
                        <path d="M12 2L2 7l10 5 10-5-10-5z" fill="currentColor"/>
                        <path d="M2 17l10 5 10-5" stroke="currentColor" fill="none" stroke-width="2"/>
                        <path d="M2 12l10 5 10-5" stroke="currentColor" fill="none" stroke-width="2"/>
                    </svg>
                    <span id="br-preview-brand-text" style="font-weight: 700; font-size: 15px; margin-left: 8px;"><?= !empty($brandName) ? $this->text->e($brandName) : 'Kanboard' ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Alternância de cards de paleta
    var paletteCards = document.querySelectorAll('.br-palette-card');
    var paletteRadios = document.querySelectorAll('.br-palette-card__radio');
    var customContainer = document.getElementById('br-custom-color-container');
    var customSwatch = document.querySelector('.br-custom-primary-swatch');
    var picker = document.getElementById('br-custom-accent-picker');
    var textInput = document.getElementById('boardrenewal_custom_accent');

    function updatePaletteSelection(selectedVal) {
        paletteCards.forEach(function(c) {
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
            if (picker) picker.value = cleanHex;
            if (customSwatch) customSwatch.style.backgroundColor = cleanHex;
        }
        if (textInput && textInput.value !== cleanHex) {
            textInput.value = cleanHex;
        }
    }

    paletteCards.forEach(function(card) {
        card.addEventListener('click', function(e) {
            var radio = this.querySelector('.br-palette-card__radio');
            if (radio) {
                updatePaletteSelection(radio.value);
            }
        });
    });

    paletteRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.checked) {
                updatePaletteSelection(this.value);
            }
        });
    });

    if (picker && textInput) {
        picker.addEventListener('input', function() {
            textInput.value = this.value;
            if (customSwatch) customSwatch.style.backgroundColor = this.value;
            selectCustomPalette();
        });
        textInput.addEventListener('input', function() {
            var v = this.value.trim();
            if (v.charAt(0) !== '#') v = '#' + v;
            if (/^#[0-9a-fA-F]{6}$/.test(v)) {
                picker.value = v;
                if (customSwatch) customSwatch.style.backgroundColor = v;
            }
            selectCustomPalette();
        });
    }

    // Chips de cores sugeridas
    var accentChips = document.querySelectorAll('.br-accent-chip-btn');
    accentChips.forEach(function(chip) {
        chip.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var color = this.getAttribute('data-color');
            if (color) {
                updateCustomAccent(color);
                selectCustomPalette();
            }
        });
    });

    // 2. Cores de Fundo dos Cards
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
        cardLightPicker.addEventListener('input', function() {
            cardLightText.value = this.value;
            updateCardPreviews();
        });
        cardLightText.addEventListener('input', function() {
            if (/^#[0-9a-fA-F]{6}$/.test(this.value)) {
                cardLightPicker.value = this.value;
            }
            updateCardPreviews();
        });
    }

    if (cardDarkPicker && cardDarkText) {
        cardDarkPicker.addEventListener('input', function() {
            cardDarkText.value = this.value;
            updateCardPreviews();
        });
        cardDarkText.addEventListener('input', function() {
            if (/^#[0-9a-fA-F]{6}$/.test(this.value)) {
                cardDarkPicker.value = this.value;
            }
            updateCardPreviews();
        });
    }

    if (resetLightBtn && cardLightText && cardLightPicker) {
        resetLightBtn.addEventListener('click', function() {
            cardLightText.value = '';
            cardLightPicker.value = '#ffffff';
            updateCardPreviews();
        });
    }

    if (resetDarkBtn && cardDarkText && cardDarkPicker) {
        resetDarkBtn.addEventListener('click', function() {
            cardDarkText.value = '';
            cardDarkPicker.value = '#1e2030';
            updateCardPreviews();
        });
    }

    // Presets rápidos
    var presetButtons = document.querySelectorAll('.br-card-preset-btn');
    presetButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
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
        textureSelect.addEventListener('change', function() {
            if (this.value === 'custom_image') {
                bgGroup.style.display = 'block';
            } else {
                bgGroup.style.display = 'none';
            }
        });
    }

    if (opacityRange && opacityText) {
        opacityRange.addEventListener('input', function() { opacityText.value = this.value; });
        opacityText.addEventListener('input', function() { opacityRange.value = this.value; });
    }

    // 4. Prévia de Logo e Nome
    var logoInput = document.getElementById('boardrenewal_logo_url');
    var brandInput = document.getElementById('boardrenewal_brand_name');
    var previewImg = document.getElementById('br-preview-logo-img');
    var previewDefaultIcon = document.getElementById('br-preview-default-icon');
    var previewText = document.getElementById('br-preview-brand-text');

    function updatePreview() {
        var url = logoInput ? logoInput.value.trim() : '';
        var name = brandInput ? brandInput.value.trim() : '';

        if (url) {
            previewImg.src = url;
            previewImg.style.display = 'inline-block';
            previewDefaultIcon.style.display = 'none';
        } else {
            previewImg.style.display = 'none';
            previewDefaultIcon.style.display = 'inline-block';
        }

        previewText.textContent = name || 'Kanboard';
    }

    if (logoInput) { logoInput.addEventListener('input', updatePreview); }
    if (brandInput) { brandInput.addEventListener('input', updatePreview); }
});
</script>
