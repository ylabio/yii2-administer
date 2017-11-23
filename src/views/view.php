<?php
/**
 * @var View $this
 * @var ActiveRecord $model
 * @var string $title
 * @var array $breadcrumbs
 * @var array $buttons
 * @var array $columns
 */

use yii\db\ActiveRecord;
use yii\web\View;
use yii\widgets\DetailView;

$this->title = $title;
$this->params['breadcrumbs'] = $breadcrumbs;
?>

<div class="administer-view">
    <p class="clear">
        <?php if (isset($buttons['update'])) : ?>
            <?= $buttons['update'] ?>
        <?php endif; ?>
        <?php if (isset($buttons['delete'])) : ?>
            <?= $buttons['delete'] ?>
        <?php endif; ?>
        <?php if (isset($buttons['index'])) : ?>
            <?= $buttons['index'] ?>
        <?php endif; ?>
        <?php if (isset($buttons['create'])) : ?>
            <?= $buttons['create'] ?>
        <?php endif; ?>
    </p>
    <div class="box box-primary">
        <?= DetailView::widget([
            'model' => $model,
        ]) ?>
    </div>
</div>