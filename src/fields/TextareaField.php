<?php

namespace ylab\administer\fields;

/**
 * Class for creation textarea field.
 */
class TextareaField extends BaseField
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        return $this->field->textarea($options)->render();
    }
}