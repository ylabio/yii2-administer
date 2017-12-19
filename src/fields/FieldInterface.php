<?php

namespace ylab\administer\fields;

/**
 * Interface for creation of form field.
 */
interface FieldInterface
{
    /**
     * Creation string representation of form field.
     * @param array $options
     * @return string
     */
    public function render(array $options = []);
}