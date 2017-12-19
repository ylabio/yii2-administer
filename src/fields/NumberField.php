<?php

namespace ylab\administer\fields;

/**
 * Class for creation of number field.
 */
class NumberField extends BaseField
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        return $this->field->input('number', $options)->render();
    }
}