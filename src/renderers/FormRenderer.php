<?php

namespace ylab\administer\renderers;

use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ylab\administer\fields\BaseField;

/**
 * Class for form rendering.
 */
class FormRenderer
{
    /**
     * Array of attributes for form rendering.
     * Example:
     * ```
     * [
     *     'name',
     *     'avatar' => [
     *         'type' => 'image',
     *     ],
     *     'doc' => [
     *         'type' => 'file',
     *     ],
     * ]
     * ```
     *
     * @var array
     */
    public $attributesInputs = [];

    /**
     * Render form and return it as a string.
     *
     * @param ActiveRecord $model
     * @param string $modelUrl
     * @param array $config
     * @return string
     * @throws InvalidConfigException
     */
    public function renderForm(ActiveRecord $model, $modelUrl, array $config)
    {
        $fields = $this->mergeConfigs($config);
        ob_start();
        ob_implicit_flush(false);
        $form = ActiveForm::begin();
        if ($model->hasErrors()) {
            echo $form->errorSummary($model, ['class' => 'callout callout-danger']);
        }
        foreach ($fields as $field => $fieldConfig) {
            $options = ArrayHelper::getValue($fieldConfig, 'options', []);
            $className = ArrayHelper::getValue($fieldConfig, 'class');
            /* @var $formField BaseField */
            $formField = \Yii::createObject($className, [$form->field($model, $field), $modelUrl]);

            if (!($formField instanceof BaseField)) {
                throw new InvalidConfigException(
                    "Field class '$className' must extends '\\ylab\\administer\\fields\\BaseField'."
                );
            }

            echo $formField->render($options);
        }
        echo Html::submitButton(
            \Yii::t('ylab/administer', $model->isNewRecord ? 'Create' : 'Save'),
            ['class' => 'btn btn-success btn-flat glyphicon-ok']
        );
        ActiveForm::end();
        return ob_get_clean();
    }

    /**
     * Merge user config and default config based on `rules()` method.
     *
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
