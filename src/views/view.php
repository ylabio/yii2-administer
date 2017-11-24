<?php
/**
 * @var View $this
 * @var ActiveRecord $model
 * @var string $title
 * @var array $breadcrumbs
 * @var AbstractButton[] $buttons
 * @var array $columns
 */

use yii\db\ActiveRecord;
use yii\web\View;
use yii\widgets\DetailView;
use ylab\administer\buttons\AbstractButton;

$this->title = $title;
$this->params['breadcrumbs'] = $breadcrumbs;
?>

<div class="administer-view">
    <p class="clear">
        <?php foreach ($buttons as $button) : ?>
            <?= $button->render() ?>
        <?php endforeach; ?>
    </p>
    <div class="box box-primary">
        <?= DetailView::widget([
            'model' => $model,
        ]) ?>
    </div>
</div>