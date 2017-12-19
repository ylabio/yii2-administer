<?php

namespace ylab\administer\fields;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * Class for creation custom widget.
 */
class WidgetField extends BaseField
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        $class = ArrayHelper::remove($options, 'class');

        if (!class_exists($class)) {
            throw new InvalidConfigException("Class name of widget must be set in 'options.class'.");
        }

        return $this->field->widget($class, $options)->render();
    }
}