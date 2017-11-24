<?php
/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var string $title
 * @var array $breadcrumbs
 * @var AbstractButton[] $buttons
 * @var array $columns
 */

use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\ArrayHelper;
use yii\web\View;
use ylab\administer\buttons\AbstractButton;

$this->title = $title;
$this->params['breadcrumbs'] = $breadcrumbs;
?>

<div class="administer-index">
    <p class="clear">
        <?php foreach ($buttons as $button) : ?>
            <?= $button->render() ?>
        <?php endforeach; ?>
    </p>
    <div class="box box-primary">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "{items}\n<div class='row'>{summary}{pager}</div>",
            'columns' => ArrayHelper::merge(
                [['class' => SerialColumn::class]],
                $columns,
                [[
                    'class' => ActionColumn::class,
                    'urlCreator' => function ($action, ActiveRecord $model) {
                        return [
                            $action,
                            'modelClass' => $this->context->modelConfig['url'],
                            'id' => $model->getPrimaryKey(),
                        ];
                    }
                ]]
            ),
        ]) ?>
    </div>
</div>