<?php
/**
 * @var View $this
 * @var string $detailView
 * @var string $title
 * @var array $breadcrumbs
 * @var Button[] $buttons
 */

use yii\web\View;
use ylab\administer\buttons\Button;

$this->title = $title;
$this->params['breadcrumbs'] = $breadcrumbs;
$this->params['buttons'] = $buttons;
?>

<div class="administer-view box box-primary">
    <?= $detailView ?>
</div>
