<?php

namespace ylab\administer\fields;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use vova07\imperavi\Widget;
use yii\helpers\Url;

/**
 * Class for creation wysiwyg imperavi widget field.
 *
 * @see https://github.com/vova07/yii2-imperavi-widget
 */
class WysiwygField extends BaseField
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        $options = ArrayHelper::merge($options, [
            'settings' => [
                'imageUpload' => Url::to([
                    'crud/image-upload',
                    'modelClass' => $this->modelUrl,
                    'field' => $this->field->attribute,
                ]),
                'imageDelete' => Url::to([
                    'crud/image-delete',
                    'modelClass' => $this->modelUrl,
                    'field' => $this->field->attribute,
                ]),
                'imageManagerJson' => Url::to([
                    'crud/images-get',
                    'modelClass' => $this->modelUrl,
                    'field' => $this->field->attribute,
                ]),
            ],
            'plugins' => [
                'imagemanager' => 'vova07\imperavi\bundles\ImageManagerAsset',
            ],
        ]);
        return $this->field->widget(Widget::class, $options)->render();
    }
}
