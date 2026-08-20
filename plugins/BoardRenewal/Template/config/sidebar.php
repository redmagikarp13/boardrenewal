<li <?= $this->app->checkMenuSelection('BoardRenewalConfigController', 'show', 'BoardRenewal') ? 'class="active"' : '' ?>>
    <?= $this->url->link(t('BoardRenewal Theme'), 'BoardRenewalConfigController', 'show', array('plugin' => 'BoardRenewal')) ?>
</li>
