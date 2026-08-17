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
        <button type="button" class="br-sidebar__collapse" id="br-sidebar-collapse" title="Recolher menu" aria-label="Recolher menu">◀</button>
    </div>

    <button type="button" class="br-sidebar__search" id="br-search-button" title="Buscar (Ctrl+K)">
        🔍 <span><?= t('Search') ?></span> <kbd>Ctrl K</kbd>
    </button>

    <nav class="br-sidebar__nav">
        <a class="br-nav-item" href="<?= $this->url->href('DashboardController', 'show', array('user_id' => $this->user->getId())) ?>">
            <span class="br-nav-item__icon">⊞</span> <span class="br-nav-item__label"><?= t('Dashboard') ?></span>
        </a>

        <div class="br-sidebar__section-title"><?= t('Projects') ?></div>

        <?php if (!empty($board_selector)): ?>
        <div class="br-sidebar__project-selector">
            <?= $this->app->component('select-dropdown-autocomplete', array(
                'name' => 'boardId',
                'placeholder' => t('Display another project'),
                'ariaLabel' => t('Display another project'),
                'items' => $board_selector,
                'redirect' => array(
                    'regex' => 'PROJECT_ID',
                    'url' => $this->url->to('BoardViewController', 'show', array('project_id' => 'PROJECT_ID')),
                ),
                'onFocus' => array(
                    'board.selector.open',
                )
            )) ?>
        </div>
        <?php endif ?>

        <?php foreach ($brProjects as $brProject):
            $brFirstName = $this->text->e($brProject['name']);
            $brRawName = $brProject['name'];
            $brFirstChar = function_exists('grapheme_substr') ? grapheme_substr($brRawName, 0, 1) : mb_substr($brRawName, 0, 1, 'UTF-8');
            $brInitial = preg_match('/[\p{L}\p{N}]/u', $brFirstChar) ? $brFirstChar : '▸';
        ?>
            <a class="br-nav-item <?= $brProject['id'] == $brCurrentProjectId ? 'br-nav-item--active' : '' ?>"
               href="<?= $this->url->href('BoardViewController', 'show', array('project_id' => $brProject['id'])) ?>">
                <span class="br-nav-item__icon" data-br-initial="<?= $this->text->e($brInitial) ?>">▸</span> <span class="br-nav-item__label"><?= $brFirstName ?></span>
            </a>
        <?php endforeach ?>
    </nav>

    <div class="br-sidebar__footer">
        <button type="button" class="br-sidebar__mode" id="br-contrast-toggle" title="<?= t('High contrast') ?>" aria-pressed="false">◐</button>
        <a class="br-nav-item br-nav-item--user" href="<?= $this->url->href('UserViewController', 'show', array('user_id' => $this->user->getId())) ?>">
            <span class="br-nav-item__icon">👤</span> <span class="br-nav-item__label"><?= $this->text->e($this->BoardRenewalHelper->getCurrentUserFullname()) ?></span>
        </a>
    </div>
</aside>
