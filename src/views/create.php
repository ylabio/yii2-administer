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

$this->title = $title;
$this->params['breadcrumbs'] = $breadcrumbs;
?>

<div class="administer-create">
    <p class="clear">
        <?php foreach ($buttons as $button) : ?>
            <?= $button->render() ?>
        <?php endforeach; ?>
    </p>
    <div class="administer-form">
        <?= $form ?>
    </div>
</div>
