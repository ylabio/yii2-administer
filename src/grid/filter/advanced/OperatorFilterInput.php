<?php

namespace ylab\administer\grid\filter\advanced;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Renders field with the ability to select an operator for searching.
 */
class OperatorFilterInput extends BaseFilterInput
{
    /**
     * @var string Name of the operator parameter.
     */
    public $operatorParam = 'operator';

    /**
     * @var array An array of operators to be substituted in drop down list.
     */
    public $operators = [];
    
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        $fieldNameOperator = $this->operatorParam . '[' . $this->attribute . ']';
        
        return Html::dropDownList(
            $fieldNameOperator,
            ArrayHelper::getValue(\Yii::$app->request->getQueryParam($this->operatorParam), $this->attribute),
            $this->operators,
            ['class' => 'form-control input-half']
        )
            . Html::textInput(
                $this->getAttribute(),
                ArrayHelper::getValue(\Yii::$app->request->getQueryParam($this->filterParam), $this->attribute),
                ['class' => 'form-control input-half']
            );
    }
}