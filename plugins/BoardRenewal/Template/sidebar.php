<aside class="br-sidebar" id="br-sidebar">
    <div class="br-sidebar__brand">
        <a href="<?= $this->url->dir() ?>">Kanboard</a>
    </div>
    <nav class="br-sidebar__nav">
        <?= $this->hook->render('template:dashboard:sidebar') ?>
    </nav>
</aside>
