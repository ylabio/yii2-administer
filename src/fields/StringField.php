<?php

namespace ylab\administer\fields;

use yii\widgets\ActiveField;

/**
 * Class for creation of string field.
 */
class StringField implements FieldInterface
{
    /**
     * @inheritdoc
     */
    public function create(ActiveField $field, array $options = [])
    {
        return $field->textInput($options)->render();
    }
}