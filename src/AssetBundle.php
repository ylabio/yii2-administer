<?php

namespace ylab\administer;

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
}
