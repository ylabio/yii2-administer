<?php

namespace ylab\administer\fields;

use yii\helpers\Html;

/**
 * Class for creation of hidden field.
 */
class HiddenField extends BaseField
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        return Html::activeHiddenInput($this->field->model, $this->field->attribute);
    }
}