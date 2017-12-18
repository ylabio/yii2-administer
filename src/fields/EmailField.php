<?php

namespace ylab\administer\fields;

use yii\widgets\ActiveField;

/**
 * Class for creation email field.
 */
class EmailField implements FieldInterface
{
    /**
     * @inheritdoc
     */
    public function create(ActiveField $field, array $options = [])
    {
        return $field->input('email', $options)->render();
    }
}