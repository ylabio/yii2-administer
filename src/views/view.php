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
        <?php foreach ($buttons as $button) : ?>
            <?= $button ?>
        <?php endforeach; ?>
    </p>
    <div class="box box-primary">
        <?= DetailView::widget([
            'model' => $model,
        ]) ?>
    </div>
</div>
