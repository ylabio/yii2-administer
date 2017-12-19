<?php

namespace ylab\administer\fields;

/**
 * Class for creation textarea field.
 */
class TextareaField extends BaseField implements FieldInterface
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        return $this->field->textarea($options)->render();
    }
}