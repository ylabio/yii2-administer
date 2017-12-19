<?php

namespace ylab\administer\fields;

/**
 * Class for creation email field.
 */
class EmailField extends BaseField implements FieldInterface
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        return $this->field->input('email', $options)->render();
    }
}