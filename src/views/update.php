<?php
/**
 * @var View $this
 * @var string $title
 * @var array $breadcrumbs
 * @var array $buttons
 * @var string $form
 */

use yii\helpers\Html;
use yii\web\View;

$this->title = $title;
$this->params['breadcrumbs'] = $breadcrumbs;
?>

<div class="administer-update">
    <h1><?= Html::encode($title) ?></h1>
    <div class="administer-buttons">
        <?php if (isset($buttons['create'])) : ?>
            <?= $buttons['create'] ?>
        <?php endif; ?>
        <?php if (isset($buttons['list'])) : ?>
            <?= $buttons['list'] ?>
        <?php endif; ?>
        <?php if (isset($buttons['delete'])) : ?>
            <?= $buttons['delete'] ?>
        <?php endif; ?>
        <?php if (isset($buttons['view'])) : ?>
            <?= $buttons['view'] ?>
        <?php endif; ?>
    </div>
    <div class="administer-form">
        <?= $form ?>
    </div>
</div>
