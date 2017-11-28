<?php

error_reporting(E_ALL);

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

$_SERVER['SCRIPT_NAME'] = '/' . __DIR__;
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

Yii::setAlias('@tests', __DIR__);
Yii::setAlias('@ylab/administer', dirname(__DIR__) . '/src');

require_once __DIR__ . '/TestCase.php';
