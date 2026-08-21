<li <?= $this->app->checkMenuSelection('UserBoardRenewalThemeController') ? 'class="active"' : '' ?>>
    <?= $this->url->link(t('Tema & Aparência'), 'UserBoardRenewalThemeController', 'show', array('plugin' => 'BoardRenewal', 'user_id' => $user['id'])) ?>
</li>
