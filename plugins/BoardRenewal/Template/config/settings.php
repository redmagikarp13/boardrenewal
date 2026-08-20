<div class="page-header">
    <h2><?= t('BoardRenewal Theme') ?> &mdash; <?= t('Customization') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('BoardRenewalConfigController', 'save', array('plugin' => 'BoardRenewal')) ?>" autocomplete="off" class="br-settings-form">
    <?= $this->form->csrf() ?>

    <?php
    $currentPalette = isset($values['boardrenewal_palette']) ? $values['boardrenewal_palette'] : 'default';
    $customAccent = isset($values['boardrenewal_custom_accent']) ? $values['boardrenewal_custom_accent'] : '#6366f1';
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
                <label class="br-palette-card <?= $currentPalette === $key ? 'br-palette-card--selected' : '' ?>">
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
                                <?php foreach ($palette['preview'] as $col): ?>
                                    <span class="br-palette-card__swatch" style="background-color: <?= $col ?>;" title="<?= $col ?>"></span>
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
            <div class="br-color-input-group">
                <input type="color" id="br-custom-accent-picker" value="<?= $this->text->e($customAccent) ?>" class="br-color-picker-input">
                <input type="text" name="boardrenewal_custom_accent" id="boardrenewal_custom_accent" value="<?= $this->text->e($customAccent) ?>" placeholder="#6366f1" class="br-color-text-input">
            </div>
            <p class="form-help"><?= t('Defina a cor primária de destaque. O tema gerará automaticamente variações de hover e fundos suaves.') ?></p>
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
    var paletteRadios = document.querySelectorAll('.br-palette-card__radio');
    var customContainer = document.getElementById('br-custom-color-container');

    paletteRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.br-palette-card').forEach(function(c) { c.classList.remove('br-palette-card--selected'); });
            if (this.checked) {
                this.closest('.br-palette-card').classList.add('br-palette-card--selected');
                if (this.value === 'custom') {
                    customContainer.style.display = 'block';
                } else {
                    customContainer.style.display = 'none';
                }
            }
        });
    });

    // Sincronização do color picker
    var picker = document.getElementById('br-custom-accent-picker');
    var textInput = document.getElementById('boardrenewal_custom_accent');
    if (picker && textInput) {
        picker.addEventListener('input', function() { textInput.value = this.value; });
        textInput.addEventListener('input', function() {
            if (/^#[0-9a-fA-F]{6}$/.test(this.value)) { picker.value = this.value; }
        });
    }

    // 2. Texturas / Imagem
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

    // 3. Prévia de Logo e Nome
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
