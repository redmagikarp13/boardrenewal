<?php
$brProjects = $this->BoardRenewalHelper->getUserProjects();
$brCurrentProjectId = isset($project['id']) ? $project['id'] : 0;
?>
<aside class="br-sidebar" id="br-sidebar">
    <div class="br-sidebar__brand">
        <a href="<?= $this->url->dir() ?>" title="Dashboard">
            <span class="br-sidebar__logo">◆</span>
            <span class="br-sidebar__name">Kanboard</span>
        </a>
    </div>

    <button type="button" class="br-sidebar__search" id="br-search-button" title="Buscar (Ctrl+K)">
        🔍 <span><?= t('Search') ?></span> <kbd>Ctrl K</kbd>
    </button>

    <nav class="br-sidebar__nav">
        <a class="br-nav-item" href="<?= $this->url->href('DashboardController', 'show', array('user_id' => $this->user->getId())) ?>">
            <span class="br-nav-item__icon">⊞</span> <?= t('Dashboard') ?>
        </a>

        <div class="br-sidebar__section-title"><?= t('Projects') ?></div>
        <?php foreach ($brProjects as $brProject): ?>
            <a class="br-nav-item <?= $brProject['id'] == $brCurrentProjectId ? 'br-nav-item--active' : '' ?>"
               href="<?= $this->url->href('BoardViewController', 'show', array('project_id' => $brProject['id'])) ?>">
                <span class="br-nav-item__icon">▸</span> <?= $this->text->e($brProject['name']) ?>
            </a>
        <?php endforeach ?>
    </nav>

    <div class="br-sidebar__footer">
        <button type="button" class="br-sidebar__mode" id="br-contrast-toggle" title="<?= t('High contrast') ?>">◐</button>
        <a class="br-nav-item" href="<?= $this->url->href('UserViewController', 'show', array('user_id' => $this->user->getId())) ?>">
            <span class="br-nav-item__icon">👤</span> <?= $this->text->e($this->BoardRenewalHelper->getCurrentUserFullname()) ?>
        </a>
    </div>
</aside>
