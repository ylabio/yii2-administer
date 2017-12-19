<?php

namespace ylab\administer\fields;

use yii\helpers\ArrayHelper;

/**
 * Class for creation field with default dropdown list.
 */
class DropdownField extends BaseField
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        $items = ArrayHelper::remove($options, 'items', []);
        return $this->field->dropDownList($items, $options)->render();
    }
}