<?php

namespace Kanboard\Plugin\BoardRenewal\Helper;

use Kanboard\Core\Base;

class BoardRenewalHelper extends Base
{
    public function isLogged()
    {
        return $this->userSession->isLogged();
    }

    /**
     * Tema do usuário atual para o atributo data-theme ('light' para visitantes)
     */
    public function getTheme()
    {
        return $this->userSession->isLogged() ? $this->userSession->getTheme() : 'light';
    }

    /**
     * Projetos ativos do usuário atual (para a sidebar)
     */
    public function getUserProjects()
    {
        if (! $this->userSession->isLogged()) {
            return array();
        }

        $ids = $this->projectPermissionModel->getActiveProjectIds($this->userSession->getId());

        return $this->projectModel->getAllByIds($ids);
    }

    /**
     * Nome exibível do usuário atual (UserSession não expõe getFullname)
     */
    public function getCurrentUserFullname()
    {
        $user = $this->userSession->getAll();

        return ! empty($user['name']) ? $user['name'] : $user['username'];
    }

    /**
     * Paletas de cores disponíveis no tema
     */
    public function getPalettes()
    {
        return array(
            'default' => array(
                'name' => 'Padrão (Modern Indigo)',
                'description' => 'Visual moderno em tons de índigo e violeta',
                'primary' => '#6366f1',
                'hover' => '#4f46e5',
                'soft' => '#eef2ff',
                'soft_dark' => '#2a2c3f',
                'contrast' => '#ffffff',
                'sidebar_active' => '#6366f1',
                'preview' => array('#6366f1', '#4f46e5', '#191a23'),
            ),
            'institutional' => array(
                'name' => 'Verde Institucional / Campus',
                'description' => 'Tons de verde e rubro com superfícies limpas (estilo institucional)',
                'primary' => '#008833',
                'hover' => '#006622',
                'soft' => '#e8f7ec',
                'soft_dark' => '#143320',
                'contrast' => '#ffffff',
                'sidebar_active' => '#008833',
                'accent_red' => '#e02b20',
                'preview' => array('#008833', '#e02b20', '#0f281e'),
            ),
            'ocean' => array(
                'name' => 'Ocean Blue',
                'description' => 'Azul dinâmico e vibrante com alto contraste',
                'primary' => '#0ea5e9',
                'hover' => '#0284c7',
                'soft' => '#e0f2fe',
                'soft_dark' => '#162e44',
                'contrast' => '#ffffff',
                'sidebar_active' => '#0ea5e9',
                'preview' => array('#0ea5e9', '#0284c7', '#0f172a'),
            ),
            'emerald' => array(
                'name' => 'Emerald Forest',
                'description' => 'Verde esmeralda equilibrado e natural',
                'primary' => '#10b981',
                'hover' => '#059669',
                'soft' => '#ecfdf5',
                'soft_dark' => '#13382c',
                'contrast' => '#ffffff',
                'sidebar_active' => '#10b981',
                'preview' => array('#10b981', '#059669', '#064e3b'),
            ),
            'crimson' => array(
                'name' => 'Crimson & Rose',
                'description' => 'Vermelho e rosa moderno de alto impacto visual',
                'primary' => '#f43f5e',
                'hover' => '#e11d48',
                'soft' => '#ffe4e6',
                'soft_dark' => '#441620',
                'contrast' => '#ffffff',
                'sidebar_active' => '#f43f5e',
                'preview' => array('#f43f5e', '#e11d48', '#1e1117'),
            ),
            'amber' => array(
                'name' => 'Warm Amber',
                'description' => 'Tons quentes de âmbar e laranja outonal',
                'primary' => '#f59e0b',
                'hover' => '#d97706',
                'soft' => '#fef3c7',
                'soft_dark' => '#3d2b12',
                'contrast' => '#ffffff',
                'sidebar_active' => '#f59e0b',
                'preview' => array('#f59e0b', '#d97706', '#261b10'),
            ),
            'custom' => array(
                'name' => 'Personalizada (Color Picker)',
                'description' => 'Defina sua própria cor de destaque hexadecimal',
                'primary' => $this->getCustomAccentColor(),
                'hover' => $this->getCustomAccentColor(),
                'soft' => '#f1f5f9',
                'soft_dark' => '#1e293b',
                'contrast' => '#ffffff',
                'sidebar_active' => $this->getCustomAccentColor(),
                'preview' => array($this->getCustomAccentColor(), '#475569', '#0f172a'),
            ),
        );
    }

    /**
     * Texturas e padrões de fundo disponíveis
     */
    public function getTextures()
    {
        return array(
            'none' => array(
                'name' => 'Nenhum (Padrão limpo)',
                'description' => 'Fundo liso original do tema',
            ),
            'polygon_emerald' => array(
                'name' => 'Polígonos Esmeralda (Low-Poly)',
                'description' => 'Mosaico poligonal em tons de verde esmeralda e floresta',
            ),
            'dots' => array(
                'name' => 'Micropontos (Dots)',
                'description' => 'Padrão sutil de pontos em grade',
            ),
            'grid' => array(
                'name' => 'Malha (Grid sutil)',
                'description' => 'Linhas geométricas discretas',
            ),
            'subtle_gradient' => array(
                'name' => 'Gradiente Suave',
                'description' => 'Gradientes radiais orgânicos de cantos',
            ),
            'custom_image' => array(
                'name' => 'Imagem Personalizada (URL)',
                'description' => 'Carrega uma imagem ou padrão via endereço web',
            ),
        );
    }

    /**
     * ID da paleta selecionada
     */
    public function getPalette()
    {
        $val = $this->configModel->get('boardrenewal_palette', 'default');
        $palettes = $this->getPalettes();
        return isset($palettes[$val]) ? $val : 'default';
    }

    /**
     * Cor customizada (quando palette == 'custom')
     */
    public function getCustomAccentColor()
    {
        $val = $this->configModel->get('boardrenewal_custom_accent', '#6366f1');
        return preg_match('/^#[0-9a-fA-F]{3,8}$/', $val) ? $val : '#6366f1';
    }

    /**
     * Textura de fundo selecionada
     */
    public function getBackgroundTexture()
    {
        $val = $this->configModel->get('boardrenewal_bg_texture', 'none');
        $textures = $this->getTextures();
        return isset($textures[$val]) ? $val : 'none';
    }

    /**
     * URL de imagem de fundo customizada
     */
    public function getCustomBackgroundUrl()
    {
        return $this->configModel->get('boardrenewal_bg_image_url', '');
    }

    /**
     * Opacidade da imagem de fundo
     */
    public function getBackgroundOpacity()
    {
        $val = $this->configModel->get('boardrenewal_bg_opacity', '0.08');
        return is_numeric($val) ? $val : '0.08';
    }

    /**
     * URL do logotipo customizado
     */
    public function getCustomLogoUrl()
    {
        return $this->configModel->get('boardrenewal_logo_url', '');
    }

    /**
     * Nome da marca / aplicação
     */
    public function getBrandName()
    {
        $val = $this->configModel->get('boardrenewal_brand_name', '');
        return !empty($val) ? $val : 'Kanboard';
    }

    /**
     * Cor de fundo dos cards no modo claro
     */
    public function getCardBgLight()
    {
        $val = $this->configModel->get('boardrenewal_card_bg_light', '');
        return preg_match('/^#[0-9a-fA-F]{3,8}$/', $val) ? $val : '';
    }

    /**
     * Cor de fundo dos cards no modo escuro
     */
    public function getCardBgDark()
    {
        $val = $this->configModel->get('boardrenewal_card_bg_dark', '');
        return preg_match('/^#[0-9a-fA-F]{3,8}$/', $val) ? $val : '';
    }

    /**
     * Gera o bloco <style> com CSS Custom Properties dinâmicas
     */
    public function renderDynamicCss()
    {
        $paletteKey = $this->getPalette();
        $palettes = $this->getPalettes();
        $palette = isset($palettes[$paletteKey]) ? $palettes[$paletteKey] : $palettes['default'];

        $primary = $palette['primary'];
        $hover = isset($palette['hover']) ? $palette['hover'] : $primary;
        $soft = isset($palette['soft']) ? $palette['soft'] : '#eef2ff';
        $softDark = isset($palette['soft_dark']) ? $palette['soft_dark'] : '#2a2c3f';
        $sidebarActive = isset($palette['sidebar_active']) ? $palette['sidebar_active'] : $primary;

        $cardBgLight = $this->getCardBgLight();
        $cardBgDark = $this->getCardBgDark();

        $texture = $this->getBackgroundTexture();
        $customBgUrl = $this->getCustomBackgroundUrl();
        $bgOpacity = $this->getBackgroundOpacity();

        $css = '<style id="br-dynamic-theme">' . "\n";

        // Variáveis de paleta
        $css .= ":root, [data-theme='light'] {\n";
        $css .= "  --br-accent: {$primary};\n";
        $css .= "  --br-accent-hover: {$hover};\n";
        $css .= "  --br-accent-soft: {$soft};\n";
        $css .= "  --br-sidebar-active: {$sidebarActive};\n";
        $css .= "  --link-color-primary: {$primary};\n";
        $css .= "  --button-primary-background-color: {$primary};\n";
        $css .= "  --button-primary-border-color: {$primary};\n";
        if (!empty($cardBgLight)) {
            $css .= "  --br-card-bg: {$cardBgLight};\n";
        }
        $css .= "}\n";

        $css .= "[data-theme='dark'] {\n";
        $css .= "  --br-accent: {$primary};\n";
        $css .= "  --br-accent-hover: {$hover};\n";
        $css .= "  --br-accent-soft: {$softDark};\n";
        $css .= "  --br-sidebar-active: {$sidebarActive};\n";
        $css .= "  --link-color-primary: {$primary};\n";
        $css .= "  --button-primary-background-color: {$primary};\n";
        $css .= "  --button-primary-border-color: {$primary};\n";
        if (!empty($cardBgDark)) {
            $css .= "  --br-card-bg: {$cardBgDark};\n";
        }
        $css .= "}\n";

        // Texturas e planos de fundo
        if ($texture === 'polygon_emerald') {
            $svgUrl = $this->helper->url->dir() . 'plugins/BoardRenewal/Assets/img/bg-polygon-emerald.svg';
            $css .= "body {\n";
            $css .= "  position: relative;\n";
            $css .= "}\n";
            $css .= "body::before {\n";
            $css .= "  content: '';\n";
            $css .= "  position: fixed;\n";
            $css .= "  inset: 0;\n";
            $css .= "  background-image: url('{$svgUrl}');\n";
            $css .= "  background-size: cover;\n";
            $css .= "  background-position: center;\n";
            $css .= "  background-attachment: fixed;\n";
            $css .= "  opacity: 0.14;\n";
            $css .= "  pointer-events: none;\n";
            $css .= "  z-index: -1;\n";
            $css .= "}\n";
            $css .= "[data-theme='dark'] body::before {\n";
            $css .= "  opacity: 0.45;\n";
            $css .= "}\n";
            $css .= ".form-login {\n";
            $css .= "  backdrop-filter: blur(12px);\n";
            $css .= "  background: color-mix(in srgb, var(--br-surface) 92%, transparent);\n";
            $css .= "}\n";
        } elseif ($texture === 'dots') {
            $css .= "body {\n";
            $css .= "  background-image: radial-gradient(var(--br-border-strong) 1px, transparent 1px);\n";
            $css .= "  background-size: 20px 20px;\n";
            $css .= "}\n";
        } elseif ($texture === 'grid') {
            $css .= "body {\n";
            $css .= "  background-image: linear-gradient(to right, var(--br-border) 1px, transparent 1px), linear-gradient(to bottom, var(--br-border) 1px, transparent 1px);\n";
            $css .= "  background-size: 24px 24px;\n";
            $css .= "}\n";
        } elseif ($texture === 'subtle_gradient') {
            $css .= "body {\n";
            $css .= "  background-image: radial-gradient(at 100% 0%, var(--br-accent-soft) 0px, transparent 45%), radial-gradient(at 0% 100%, var(--br-surface-2) 0px, transparent 45%);\n";
            $css .= "  background-attachment: fixed;\n";
            $css .= "}\n";
        } elseif ($texture === 'custom_image' && !empty($customBgUrl)) {
            $safeUrl = htmlspecialchars($customBgUrl, ENT_QUOTES, 'UTF-8');
            $css .= "body {\n";
            $css .= "  position: relative;\n";
            $css .= "}\n";
            $css .= "body::before {\n";
            $css .= "  content: '';\n";
            $css .= "  position: fixed;\n";
            $css .= "  inset: 0;\n";
            $css .= "  background-image: url('{$safeUrl}');\n";
            $css .= "  background-size: cover;\n";
            $css .= "  background-position: center;\n";
            $css .= "  background-attachment: fixed;\n";
            $css .= "  opacity: {$bgOpacity};\n";
            $css .= "  pointer-events: none;\n";
            $css .= "  z-index: -1;\n";
            $css .= "}\n";
        }

        $css .= "</style>\n";

        return $css;
    }
}
