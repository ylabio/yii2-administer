<?php
/**
 * @var View $this
 * @var string $title
 * @var array $breadcrumbs
 * @var Button[] $buttons
 * @var string $form
 */

use yii\web\View;
use ylab\administer\buttons\Button;

$this->title = $title;
$this->params['breadcrumbs'] = $breadcrumbs;
$this->params['buttons'] = $buttons;
?>

<div class="administer-form">
    <?= $form ?>
</div>
