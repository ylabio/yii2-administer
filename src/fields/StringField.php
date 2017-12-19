<?php

namespace ylab\administer\fields;

/**
 * Class for creation of string field.
 */
class StringField extends BaseField implements FieldInterface
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        return $this->field->textInput($options)->render();
    }
}