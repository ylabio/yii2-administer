<?php

namespace ylab\administer\fields;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveField;

/**
 * Class for creation custom widget.
 */
class WidgetField implements FieldInterface
{
    /**
     * @inheritdoc
     */
    public function create(ActiveField $field, array $options = [])
    {
        $class = ArrayHelper::remove($options, 'class');

        if (!class_exists($class)) {
            throw new InvalidConfigException("Class name of widget must be set in 'options.class'.");
        }

        return $field->widget($class, $options)->render();
    }
}