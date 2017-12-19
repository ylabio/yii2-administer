<?php

namespace ylab\administer\fields;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/**
 * Class for creation of file field with image preview.
 */
class ImageField extends BaseField
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        $id = 'image-' . StringHelper::basename(get_class($this->field->model)) . "-{$this->field->attribute}";
        $content = isset($this->field->model->{$this->field->attribute})
            ? Html::img($this->field->model->{$this->field->attribute}, ['width' => 200])
            : '';
        $options = ArrayHelper::merge(['onchange' => "showImage(this, '$id')"], $options);
        return $this->field->fileInput($options)->render() . Html::tag('div', $content, ['id' => $id]);
    }
}