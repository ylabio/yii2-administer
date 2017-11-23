<?php
/**
 * @var View $this
 * @var string $content
 */

use dmstr\widgets\Alert;
use dmstr\widgets\Menu;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use ylab\administer\AssetBundle;

AssetBundle::register($this);
$title = $this->title === null ? \Yii::$app->name : $this->title . ' | ' . \Yii::$app->name;
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($title) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<?php $this->beginBody() ?>
<div class="wrapper">

    <header class="main-header">
        <?= Html::a(
            '<span class="logo-mini">APP</span><span class="logo-lg">' . Yii::$app->name . '</span>',
            '/admin',
            ['class' => 'logo']
        ) ?>
        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
        </nav>
    </header>

    <aside class="main-sidebar">
        <section class="sidebar">
            <?= Menu::widget([
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => $this->context->module->getMenuItems(),
            ]) ?>
        </section>
    </aside>

    <div class="content-wrapper">
        <section class="content-header">
            <?php if ($this->title !== null) : ?>
                <h1><?= Html::encode($this->title) ?></h1>
            <?php endif; ?>
            <?= Breadcrumbs::widget([
                'homeLink' => [
                    'label' => '<i class="fa fa-dashboard"></i>' . Yii::t('yii', 'Home'),
                    'url' => '/admin',
                    'encode' => false,
                ],
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
        </section>

        <section class="content">
            <?= Alert::widget() ?>
            <?= $content ?>
        </section>
    </div>

    <footer class="main-footer"></footer>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
