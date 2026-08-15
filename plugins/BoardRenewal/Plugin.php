<?php

namespace Kanboard\Plugin\BoardRenewal;

use Kanboard\Core\Plugin\Base;

class Plugin extends Base
{
    public function initialize()
    {
        $this->template->setTemplateOverride('layout', 'boardrenewal:layout');
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
        return '0.1.0';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/magikarp13/boardrenewal';
    }
}
