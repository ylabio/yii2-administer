<?php
/**
 * @var View $this
 * @var string $title
 * @var array $breadcrumbs
 * @var AbstractButton[] $buttons
 * @var string $form
 */

use yii\web\View;
use ylab\administer\buttons\AbstractButton;
use ylab\administer\widgets\ButtonsWidget;

$this->title = $title;
$this->params['breadcrumbs'] = $breadcrumbs;
$this->params['buttons'] = $buttons;
?>

<div class="administer-form">
    <?= $form ?>
</div>
