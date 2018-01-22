<?php

namespace ylab\administer\grid\filter\base;

use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;

/**
 * Input field of filter with date intervals selection.
 * Base on kartik-v/yii2-date-range.
 * @see https://github.com/kartik-v/yii2-date-range
 */
class DateIntervalFilterInput extends BaseFilterInput
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        $options = ArrayHelper::merge(
            $options,
            [
                'model' => $this->model,
                'attribute' => $this->attribute,
                'convertFormat' => true,
                'pluginOptions' => [
                    'timePicker' => true,
                    'timePickerIncrement' => 15,
                    'locale' => ['format' => 'Y-m-d H:i'],
                ],
            ]
        );
        return DateRangePicker::widget($options);
    }
}