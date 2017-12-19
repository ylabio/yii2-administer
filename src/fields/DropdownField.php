<?php

namespace ylab\administer\fields;

use yii\helpers\ArrayHelper;
use yii\widgets\ActiveField;

/**
 * Class for creation field with default dropdown list.
 */
class DropdownField implements FieldInterface
{
    /**
     * @inheritdoc
     */
    public function create(ActiveField $field, array $options = [])
    {
        $items = ArrayHelper::remove($options, 'items', []);
        return $field->dropDownList($items, $options)->render();
    }
}