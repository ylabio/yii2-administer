<?php

namespace ylab\administer\grid\filter\advanced;

use yii\web\AssetBundle;

/**
 * This asset bundle provides the javascript and css files for the [[GridView]] widget.
 */
class AdvancedFilterAsset extends AssetBundle
{
    public $sourcePath = '@ylab/administer/assets';
    public $js = [
        'js/advanced-filter.js',
    ];
    public $css = [
        'css/advanced-filter.css',
    ];
    public $depends = [
        'ylab\administer\assets\AssetBundle',
    ];
}
