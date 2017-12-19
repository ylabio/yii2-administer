<?php

namespace ylab\administer\fields;

use yii\widgets\ActiveField;

/**
 * Class for creation textarea field.
 */
class TextareaField implements FieldInterface
{
    /**
     * @inheritdoc
     */
    public function create(ActiveField $field, array $options = [])
    {
        return $field->textarea($options)->render();
    }
}