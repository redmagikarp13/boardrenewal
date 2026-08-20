<?php

namespace Kanboard\Plugin\BoardRenewal;

use Kanboard\Core\Plugin\Base;

class Plugin extends Base
{
    public function initialize()
    {
        $this->template->setTemplateOverride('layout', 'boardRenewal:layout');
        // Página de criação de tarefa aberta em outra guia: envolve com o layout do tema
        $this->template->setTemplateOverride('task_creation/show', 'boardRenewal:task_creation/show');
        // Página de login moderna com suporte a logo e identidade visual
        $this->template->setTemplateOverride('auth/index', 'boardRenewal:auth/index');

        // Hook na barra lateral de Configurações
        $this->template->hook->attach('template:config:sidebar', 'boardRenewal:config/sidebar');
    }

    public function getHelpers()
    {
        return array(
            'Plugin\BoardRenewal\Helper' => array('BoardRenewalHelper'),
        );
    }

    public function getPluginName()
    {
        return 'BoardRenewal';
    }

    public function getPluginDescription()
    {
        return 'Modern responsive theme with dark mode, high contrast and per-project customization';
    }

    public function getPluginAuthor()
    {
        return 'magikarp13';
    }

    public function getPluginVersion()
    {
        return '0.2.0';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/magikarp13/boardrenewal';
    }
}
