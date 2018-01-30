<?php

namespace ylab\administer\grid\filter\advanced;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Renders the dropdown select field.
 */
class SelectFilterInput extends BaseFilterInput
{
    /**
     * @var array Values for dropdown menu.
     */
    public $values = [];

    /**
     * @inheritdoc
     */
    public function render()
    {
        return Html::dropDownList(
            $this->getAttribute(),
            ArrayHelper::getValue(\Yii::$app->request->getQueryParam($this->filterParam), $this->attribute),
            $this->values,
            ['class' => 'form-control']
        );
    }
}