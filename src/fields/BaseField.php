<?php

namespace ylab\administer\fields;

use yii\widgets\ActiveField;

/**
 * Basic class for implementation of fields.
 */
abstract class BaseField
{
    /**
     * @var ActiveField field object of form
     */
    protected $field;

    /**
     * @param ActiveField $field
     */
    public function __construct(ActiveField $field)
    {
        $this->field = $field;
    }
}