<?php

namespace ylab\administer\fields;

use yii\widgets\ActiveField;

/**
 * Interface for creation of form field.
 */
interface FieldInterface
{
    /**
     * Creation string representation of form field.
     * @param ActiveField $field
     * @param array $options
     * @return string
     */
    public function create(ActiveField $field, array $options = []);
}