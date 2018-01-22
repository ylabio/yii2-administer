<?php

namespace ylab\administer\grid\filter\advanced;

use kartik\daterange\DateRangePicker;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/**
 * Input field of filter with date intervals selection.
 * Base on kartik-v/yii2-date-range.
 * @see https://github.com/kartik-v/yii2-date-range
 */
class DateIntervalFilterInput extends OperatorFilterInput
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        $val = ArrayHelper::getValue(\Yii::$app->request->getQueryParam($this->filterParam), $this->attribute);
        $options = ArrayHelper::merge(
            $options,
            [
                'name' => $this->getAttribute(),
                'value' => ArrayHelper::getValue(\Yii::$app->request->getQueryParam($this->filterParam), $this->attribute),
                'convertFormat' => true,
                'pluginOptions' => [
                    'timePicker' => true,
                    'timePickerIncrement' => 15,
                    'locale' => ['format' => 'Y-m-d H:i'],
                ],
            ]
        );
        $operatorHidden = Html::hiddenInput($this->operatorParam . '[' . $this->attribute . ']', 'range');

        return $operatorHidden . DateRangePicker::widget($options);
    }
}