<?php
$brProjects = $this->BoardRenewalHelper->getUserProjects();
$brCurrentProjectId = isset($project['id']) ? $project['id'] : 0;
?>
<aside class="br-sidebar" id="br-sidebar">
    <div class="br-sidebar__brand">
        <a href="<?= $this->url->dir() ?>" title="Dashboard">
            <svg class="br-icon br-icon--lg" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                <path d="M2 17l10 5 10-5"/>
                <path d="M2 12l10 5 10-5"/>
            </svg>
            <span class="br-sidebar__name">Kanboard</span>
        </a>
        <button type="button" class="br-sidebar__collapse" id="br-sidebar-collapse" title="Recolher menu" aria-label="Recolher menu">
            <svg class="br-icon" viewBox="0 0 24 24" aria-hidden="true">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
        </button>
    </div>

    <button type="button" class="br-sidebar__search" id="br-search-button" title="Buscar (Ctrl+K)">
        <svg class="br-icon" viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <span><?= t('Search') ?></span> <kbd>Ctrl K</kbd>
    </button>

    <nav class="br-sidebar__nav">
        <a class="br-nav-item" href="<?= $this->url->href('DashboardController', 'show', array('user_id' => $this->user->getId())) ?>">
            <svg class="br-icon br-nav-item__icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="3" width="7" height="7"/>
                <rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/>
            </svg>
            <span class="br-nav-item__label"><?= t('Dashboard') ?></span>
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
            $brInitial = preg_match('/[\p{L}\p{N}]/u', $brFirstChar) ? mb_strtoupper($brFirstChar, 'UTF-8') : mb_substr($brFirstName, 0, 1, 'UTF-8');
        ?>
            <a class="br-nav-item <?= $brProject['id'] == $brCurrentProjectId ? 'br-nav-item--active' : '' ?>"
               href="<?= $this->url->href('BoardViewController', 'show', array('project_id' => $brProject['id'])) ?>">
                <svg class="br-icon br-nav-item__icon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
                <span class="br-nav-item__initial"><?= $this->text->e($brInitial) ?></span>
                <span class="br-nav-item__label"><?= $brFirstName ?></span>
            </a>
        <?php endforeach ?>
    </nav>

    <div class="br-sidebar__footer">
        <button type="button" class="br-sidebar__mode" id="br-contrast-toggle" title="<?= t('High contrast') ?>" aria-pressed="false">
            <svg class="br-icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 2a10 10 0 0 1 0 20z" fill="currentColor"/>
            </svg>
        </button>
        <a class="br-nav-item br-nav-item--user" href="<?= $this->url->href('UserViewController', 'show', array('user_id' => $this->user->getId())) ?>">
            <svg class="br-icon br-nav-item__icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            <span class="br-nav-item__label"><?= $this->text->e($this->BoardRenewalHelper->getCurrentUserFullname()) ?></span>
        </a>
    </div>
</aside>
