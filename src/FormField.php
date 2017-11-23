<?php

namespace ylab\administer;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\widgets\ActiveField;

/**
 * Class FormField for creating form fields based on model attribute type.
 */
class FormField
{
    /**
     * @var string
     */
    const TYPE_EMAIL = 'email';
    /**
     * @var string
     */
    const TYPE_IMAGE = 'image';
    /**
     * @var string
     */
    const TYPE_FILE = 'file';
    /**
     * @var string
     */
    const TYPE_NUMBER = 'number';
    /**
     * @var string
     */
    const TYPE_STRING = 'string';

    /**
     * Create form field based on one of types.
     *
     * @param ActiveField $field
     * @param string $type
     * @param array $options
     * @return string
     * @throws InvalidConfigException
     */
    public static function createField(ActiveField $field, $type, array $options = [])
    {
        switch ($type) {
            case static::TYPE_EMAIL:
                return $field->input('email', $options)->render();
            case static::TYPE_IMAGE:
                $id = 'image-' . StringHelper::basename(get_class($field->model)) . "-$field->attribute";
                $content = isset($field->model->{$field->attribute})
                    ? Html::img($field->model->{$field->attribute}, ['width' => 200])
                    : '';
                $options = ArrayHelper::merge(['onchange' => "showImage(this, '$id')"], $options);
                return $field->fileInput($options)->render() . Html::tag('div', $content, ['id' => $id]);
            case static::TYPE_FILE:
                return $field->fileInput($options)->render();
            case static::TYPE_NUMBER:
                return $field->input('number', $options)->render();
            case static::TYPE_STRING:
                return $field->textInput($options)->render();
            default:
                throw new InvalidConfigException("Undefined field type: '$type'.");
        }
    }
}
