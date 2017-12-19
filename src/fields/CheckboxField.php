<?php

namespace ylab\administer\fields;

use yii\helpers\ArrayHelper;

/**
 * Class for creation checkbox field.
 */
class CheckboxField extends BaseField implements FieldInterface
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        $enclosedByLabel = (bool)ArrayHelper::remove($options, 'enclosedByLabel', true);
        return $this->field->checkbox($options, $enclosedByLabel)->render();
    }
}