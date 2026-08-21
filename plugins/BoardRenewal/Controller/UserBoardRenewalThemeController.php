<?php

namespace Kanboard\Plugin\BoardRenewal\Controller;

use Kanboard\Controller\BaseController;

class UserBoardRenewalThemeController extends BaseController
{
    public function show(array $values = array(), array $errors = array())
    {
        $user = $this->getUser();

        if (empty($values)) {
            $values = $this->userMetadataModel->getAll($user['id']);
        }

        $this->response->html($this->helper->layout->user('boardRenewal:user/theme', array(
            'user' => $user,
            'values' => $values,
            'errors' => $errors,
            'palettes' => $this->helper->BoardRenewalHelper->getPalettes(),
            'globalPalette' => $this->configModel->get('boardrenewal_palette', 'default'),
            'title' => t('Tema & Aparência'),
        )));
    }

    public function save()
    {
        $user = $this->getUser();
        $values = $this->request->getValues();

        $keys = array(
            'boardrenewal_palette',
            'boardrenewal_custom_accent',
            'boardrenewal_card_bg_light',
            'boardrenewal_card_bg_dark',
        );

        $toSave = array();
        foreach ($keys as $key) {
            if (isset($values[$key])) {
                $val = trim($values[$key]);
                if (in_array($key, array('boardrenewal_custom_accent', 'boardrenewal_card_bg_light', 'boardrenewal_card_bg_dark'), true)) {
                    if (!empty($val) && $val[0] !== '#') {
                        $val = '#' . $val;
                    }
                }
                $toSave[$key] = $val;
            }
        }

        $this->userMetadataModel->save($user['id'], $toSave);
        $this->flash->success(t('Suas preferências de tema foram salvas com sucesso!'));

        $this->response->redirect($this->helper->url->to('UserBoardRenewalThemeController', 'show', array('plugin' => 'BoardRenewal', 'user_id' => $user['id'])));
    }
}
