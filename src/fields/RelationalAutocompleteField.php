<?php

namespace ylab\administer\fields;

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * Class for creation of field with autocomplete for related data.
 */
class RelationalAutocompleteField extends BaseField
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [])
    {
        return $this->field->widget(Select2::className(), $this->mergeOptions($options))->render();
    }

    /**
     * Merge options with default.
     * @param array $options
     * @return array
     */
    protected function mergeOptions(array $options)
    {
        $relation = ArrayHelper::getValue($this->field, 'attribute');
        $key = ArrayHelper::remove($options, 'keyAttribute', 'id');
        $label = ArrayHelper::remove($options, 'labelAttribute');
        $defaultOptions = [
            'initValueText' => ArrayHelper::getValue($this->field, "model.{$relation}.{$label}"),
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'ajax' => [
                    'url' => $this->createUrl($relation, $key, $label),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(item) { return item.text; }'),
                'templateSelection' => new JsExpression('function (item) { return item.text; }'),
            ],
        ];
        return ArrayHelper::merge($defaultOptions, $options);
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
        $id = ArrayHelper::getValue($this->field, 'model.id');
        return Url::to([
            'autocomplete',
            'modelClass' => $this->modelUrl,
            'id' => $id,
            'relation' => $relation,
            'key' => $key,
            'label' => $label,
        ]);
    }
}