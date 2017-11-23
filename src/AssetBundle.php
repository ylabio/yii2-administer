<?php

namespace ylab\administer;

use dmstr\web\AdminLteAsset;

/**
 * @inheritdoc
 */
class AssetBundle extends \yii\web\AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@ylab/administer/assets';
    /**
     * @inheritdoc
     */
    public $js = [
        'js/image-preview.js',
    ];
    /**
     * @inheritdoc
     */
    public $css = [
        'css/administer.css',
    ];
    /**
     * @inheritdoc
     */
    public $depends = [
        AdminLteAsset::class,
    ];
}
