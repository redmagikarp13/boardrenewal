<?php
$brBrandName = $this->BoardRenewalHelper->getBrandName();
$brLogoUrl = $this->BoardRenewalHelper->getCustomLogoUrl();
?>
<div class="form-login br-login-container">
    <div class="br-login-header">
        <?php if (!empty($brLogoUrl)): ?>
            <img src="<?= $this->text->e($brLogoUrl) ?>" alt="<?= $this->text->e($brBrandName) ?>" class="br-login-logo">
        <?php else: ?>
            <div class="br-login-default-icon">
                <svg class="br-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 2L2 7l10 5 10-5-10-5z" fill="currentColor"/>
                    <path d="M2 17l10 5 10-5" stroke="currentColor" fill="none" stroke-width="2"/>
                    <path d="M2 12l10 5 10-5" stroke="currentColor" fill="none" stroke-width="2"/>
                </svg>
            </div>
        <?php endif ?>
        <h1 class="br-login-title"><?= $this->text->e($brBrandName) ?></h1>
        <p class="br-login-subtitle"><?= t('Entre com suas credenciais para continuar') ?></p>
    </div>

    <?= $this->hook->render('template:auth:login-form:before') ?>

    <form method="post" action="<?= $this->url->href('AuthController', 'check') ?>">
        <?= $this->form->csrf() ?>

        <?= $this->form->label(t('Username'), 'form-username') ?>
        <?= $this->form->text('username', $values, $errors, array('autofocus', 'required', 'tabindex="1"', 'placeholder' => t('Username'))) ?>

        <?= $this->form->label(t('Password'), 'form-password') ?>
        <?= $this->form->password('password', $values, $errors, array('required', 'tabindex="2"', 'placeholder' => t('Password'))) ?>

        <?php if (isset($captcha) && $captcha): ?>
            <?= $this->form->label(t('Enter the text below'), 'captcha') ?>
            <img src="<?= $this->url->href('AuthController', 'captcha') ?>" alt="Captcha">
            <?= $this->form->text('captcha', array(), $errors, array('required', 'tabindex="3"')) ?>
        <?php endif ?>

        <div class="br-login-options">
            <?= $this->form->checkbox('remember_me', t('Remember Me'), 1, true, '', array('tabindex="4"')) ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-blue btn-full" tabindex="5"><?= t('Sign in') ?></button>
        </div>

        <?php if ($this->app->config('password_reset') == 1): ?>
            <div class="reset-password">
                <?= $this->url->link(t('Forgot password?'), 'PasswordResetController', 'create') ?>
            </div>
        <?php endif ?>
    </form>

    <?= $this->hook->render('template:auth:login-form:after') ?>
</div>
