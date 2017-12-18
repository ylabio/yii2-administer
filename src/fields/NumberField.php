<?php

namespace ylab\administer\fields;

use yii\widgets\ActiveField;

/**
 * Class for creation of number field.
 */
class NumberField implements FieldInterface
{
    /**
     * @inheritdoc
     */
    public function create(ActiveField $field, array $options = [])
    {
        return $field->input('number', $options)->render();
    }
}