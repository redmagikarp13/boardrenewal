<?php

namespace Kanboard\Plugin\BoardRenewal\Controller;

use Kanboard\Controller\BaseController;

class BoardRenewalConfigController extends BaseController
{
    /**
     * Exibe a página de configurações do tema BoardRenewal
     */
    public function show()
    {
        $this->response->html($this->helper->layout->config('boardRenewal:config/settings', array(
            'title' => t('Settings') . ' &gt; ' . t('BoardRenewal Theme'),
            'values' => $this->configModel->getAll(),
            'palettes' => $this->helper->BoardRenewalHelper->getPalettes(),
            'textures' => $this->helper->BoardRenewalHelper->getTextures(),
            'errors' => array(),
        )));
    }

    /**
     * Salva as configurações do tema BoardRenewal
     */
    public function save()
    {
        $values = $this->request->getValues();

        // Sanitização básica das chaves do plugin
        $keys = array(
            'boardrenewal_palette',
            'boardrenewal_custom_accent',
            'boardrenewal_bg_texture',
            'boardrenewal_bg_image_url',
            'boardrenewal_bg_opacity',
            'boardrenewal_logo_url',
            'boardrenewal_brand_name',
        );

        $toSave = array();
        foreach ($keys as $key) {
            if (isset($values[$key])) {
                $toSave[$key] = trim($values[$key]);
            }
        }

        if (!empty($toSave) && $this->configModel->save($toSave)) {
            $this->flash->success(t('Settings saved successfully.'));
        } else {
            $this->flash->failure(t('Unable to save your settings.'));
        }

        $this->response->redirect($this->helper->url->to('BoardRenewalConfigController', 'show', array('plugin' => 'BoardRenewal')));
    }
}
