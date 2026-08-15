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
}
