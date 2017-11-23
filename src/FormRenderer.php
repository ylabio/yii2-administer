<?php

namespace ylab\administer;

use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

class FormRenderer
{
    /**
     * @var array
     */
    public $attributesInputs = [];

    /**
     * @param ActiveRecord $model
     * @param array $config
     * @return string
     * @throws InvalidConfigException
     */
    public function renderForm(ActiveRecord $model, array $config)
    {
        $fields = $this->mergeConfigs($config);
        ob_start();
        ob_implicit_flush(false);
        $form = ActiveForm::begin();
        if ($model->hasErrors()) {
            echo $form->errorSummary($model, ['class' => 'callout callout-danger']);
        }
        foreach ($fields as $field => $fieldConfig) {
            $options = isset($fieldConfig['options']) ? $fieldConfig['options'] : [];
            echo FormField::createField($form->field($model, $field), $fieldConfig['type'], $options);
        }
        echo Html::submitButton(
            \Yii::t('ylab/administer', $model->isNewRecord ? 'Create' : 'Save'),
            ['class' => 'btn btn-success btn-flat glyphicon-ok']
        );
        ActiveForm::end();
        return ob_get_clean();
    }

    /**
     * @param array $defaultConfig
     * @return array
     * @throws InvalidConfigException
     */
    protected function mergeConfigs(array $defaultConfig)
    {
        if (empty($this->attributesInputs)) {
            return $defaultConfig;
        }

        $config = [];
        foreach ($this->attributesInputs as $attribute => $params) {
            if (is_array($params)) {
                $config[$attribute] = $params;
            } elseif (isset($defaultConfig[$params])) {
                $config[$params] = $defaultConfig[$params];
            } else {
                throw new InvalidConfigException('Each "attributesInputs" item must be string or array.');
            }
        }

        return $config;
    }
}
