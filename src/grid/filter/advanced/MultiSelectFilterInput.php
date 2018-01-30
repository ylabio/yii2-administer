<?php

namespace ylab\administer\grid\filter\advanced;

use kartik\select2\Select2;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * Multiselect field of filter with autocomplete.
 * Based on kartik-v/yii2-widget-select2.
 * @see https://github.com/kartik-v/yii2-widget-select2
 */
class MultiSelectFilterInput extends OperatorFilterInput
{
    /**
     * @var ActiveRecord Model for searching records from the current filter state.
     */
    public $modelClass;

    /**
     * @var string The operator that will be used for filtering.
     */
    public $operator;

    /**
     * @var string The attribute of the associated entity, in which the match will be searched for auto-complete.
     */
    public $relationAttribute;

    /**
     * @inheritdoc
     */
    public function render()
    {
        $options = $this->options;
        $relation = $this->relationAttribute;
        $key = ArrayHelper::remove($options, 'keyAttribute', 'id');
        $label = ArrayHelper::remove($options, 'labelAttribute', 'title');
        $modelClass = $this->modelClass;
        $params = ArrayHelper::getValue(\Yii::$app->request->getQueryParam($this->filterParam), $this->attribute);
        $options = ArrayHelper::merge(
            $options,
            [
                'name' => $this->getAttribute(),
                'value' => ArrayHelper::map($modelClass::findAll($params), $key, $key),
                'initValueText' => ArrayHelper::map($modelClass::findAll($params), $key, $label),
                'options' => [
                    'multiple' => true,
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 1,
                    'ajax' => [
                        'url' => $this->createUrl($relation, $key, $label),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                ],
            ]
        );
        $operatorHidden = (!empty($this->operator))
            ? Html::hiddenInput($this->operatorParam . '[' . $this->attribute . ']', $this->operator)
            : '';

        return $operatorHidden . Select2::widget($options);
    }

    /**
     * Create URL for autocomplete queries.
     *
     * @param string $relation name of relation
     * @param string $key attribute from related model for key
     * @param string $label attribute from related model for display
     * @return string
     */
    protected function createUrl($relation, $key, $label)
    {
        return Url::to([
            'api/autocomplete',
            'modelClass' => $this->modelUrl,
            'relation' => $relation,
            'key' => $key,
            'label' => $label,
        ]);
    }
}