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

<div class="administer-create">
    <h1><?= Html::encode($title) ?></h1>
    <div class="administer-buttons">
        <?php if (isset($buttons['list'])) : ?>
            <?= $buttons['list'] ?>
        <?php endif; ?>
    </div>
    <div class="administer-form">
        <?= $form ?>
    </div>
</div>
