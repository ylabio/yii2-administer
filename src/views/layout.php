<?php
/**
 * @var View $this
 * @var string $content
 */

use yii\web\View;
use ylab\administer\AssetBundle;

AssetBundle::register($this);

$this->beginPage();
$this->head();
$this->beginBody();

echo $content;

$this->endBody();
$this->endPage();
