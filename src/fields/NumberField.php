<?php

namespace ylab\administer\fields;

/**
 * Class for creation of number field.
 */
class NumberField extends BaseField implements FieldInterface
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        return $this->field->input('number', $options)->render();
    }
}