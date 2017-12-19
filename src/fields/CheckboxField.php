<?php

namespace ylab\administer\fields;

use yii\helpers\ArrayHelper;
use yii\widgets\ActiveField;

/**
 * Class for creation checkbox field.
 */
class CheckboxField implements FieldInterface
{
    /**
     * @inheritdoc
     */
    public function create(ActiveField $field, array $options = [])
    {
        $enclosedByLabel = (bool)ArrayHelper::remove($options, 'enclosedByLabel', true);
        return $field->checkbox($options, $enclosedByLabel)->render();
    }
}