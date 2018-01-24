<?php

namespace ylab\administer\fields;

/**
 * Class for creation of password field.
 */
class PasswordField extends BaseField
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        return $this->field->passwordInput($options)->render();
    }
}