<?php
// Quando aberto via modal (AJAX), renderiza só o fragmento.
// Quando aberto direto em outra guia, envolve com o layout do tema.
if ($this->app->isAjax()): ?>
    <?= $this->render('boardRenewal:task_creation/form', array(
        'project' => $project,
        'errors' => $errors,
        'values' => $values,
        'columns_list' => $columns_list,
        'users_list' => $users_list,
        'categories_list' => $categories_list,
        'swimlanes_list' => $swimlanes_list,
        'screenshot' => $screenshot,
        'files' => $files,
    )) ?>
<?php else: ?>
    <?= $this->helper->layout->pageLayout('boardRenewal:task_creation/form', array(
        'title' => $project['name'] . ' &gt; ' . t('New task'),
        'project' => $project,
        'errors' => $errors,
        'values' => $values,
        'columns_list' => $columns_list,
        'users_list' => $users_list,
        'categories_list' => $categories_list,
        'swimlanes_list' => $swimlanes_list,
        'screenshot' => $screenshot,
        'files' => $files,
    )) ?>
<?php endif ?>
