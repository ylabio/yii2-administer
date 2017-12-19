<?php

namespace ylab\administer\fields;

/**
 * Class for creation of file field.
 */
class FileField extends BaseField implements FieldInterface
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        return $this->field->fileInput($options)->render();
    }
}