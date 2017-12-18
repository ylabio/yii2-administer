<?php

namespace ylab\administer\fields;

use yii\widgets\ActiveField;

/**
 * Class for creation of file field.
 */
class FileField implements FieldInterface
{
    /**
     * @inheritdoc
     */
    public function create(ActiveField $field, array $options = [])
    {
        return $field->fileInput($options)->render();
    }
}