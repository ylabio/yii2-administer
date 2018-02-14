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
     * @var string URL of entity
     */
    protected $modelUrl;

    /**
     * @param ActiveField $field
     * @param string $modelUrl
     */
    public function __construct(ActiveField $field, $modelUrl)
    {
        $this->field = $field;
        $this->modelUrl = $modelUrl;
    }

    /**
     * @return ActiveField
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Creation string representation of form field.
     * @param array $options
     * @return string
     */
    abstract public function render(array $options = []);
}