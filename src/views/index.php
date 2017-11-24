<?php
/**
 * @var View $this
 * @var string $gridView
 * @var string $title
 * @var array $breadcrumbs
 * @var array $buttons
 */

use yii\web\View;

$this->title = $title;
$this->params['breadcrumbs'] = $breadcrumbs;
?>

<div class="administer-index">
    <p class="clear">
        <?php foreach ($buttons as $button) : ?>
            <?= $button ?>
        <?php endforeach; ?>
    </p>
    <div class="box box-primary">
        <?= $gridView ?>
    </div>
</div>
