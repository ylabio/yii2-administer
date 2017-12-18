<?php

namespace ylab\administer\fields;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\widgets\ActiveField;

/**
 * Class for creation of file field with image preview.
 */
class ImageField implements FieldInterface
{
    /**
     * @inheritdoc
     */
    public function create(ActiveField $field, array $options = [])
    {
        $id = 'image-' . StringHelper::basename(get_class($field->model)) . "-$field->attribute";
        $content = isset($field->model->{$field->attribute})
            ? Html::img($field->model->{$field->attribute}, ['width' => 200])
            : '';
        $options = ArrayHelper::merge(['onchange' => "showImage(this, '$id')"], $options);
        return $field->fileInput($options)->render() . Html::tag('div', $content, ['id' => $id]);
    }
}