<?php

namespace ylab\administer;

use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\widgets\ActiveForm;

/**
 * Class for form rendering
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
     * @var string
     */
    public $layout = '@ylab/administer/views/layout.php';

    /**
     * Render form.
     *
     * @param ActiveRecord $model
     * @param array $config
     * @return string
     * @throws InvalidConfigException
     */
    public function render(ActiveRecord $model, array $config)
    {
        $viewFile = $this->getViewFile($model);
        $title = $this->getTitle($model);
        $breadcrumbs = $this->getBreadcrumbs($model);
        $buttons = $this->getButtons($model);
        $form = $this->renderForm($model, $this->mergeConfigs($config));
        $view = \Yii::$app->getView();
        return $view->renderFile(
            $this->layout,
            ['content' => $view->render($viewFile, compact('title', 'breadcrumbs', 'buttons', 'form'), $this)],
            $this
        );
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

    /**
     * Render form.
     *
     * @param ActiveRecord $model
     * @param array $fields
     * @return string
     * @throws InvalidConfigException
     */
    protected function renderForm(ActiveRecord $model, array $fields)
    {
        ob_start();
        ob_implicit_flush(false);
        $form = ActiveForm::begin();
        foreach ($fields as $field => $config) {
            $options = isset($config['options']) ? $config['options'] : [];
            echo FormField::createField($form->field($model, $field), $config['type'], $options);
        }
        echo Html::submitButton(\Yii::t('ylab/administer', $model->isNewRecord ? 'Create' : 'Update'));
        ActiveForm::end();
        return ob_get_clean();
    }

    /**
     * Get view file for concrete action.
     *
     * @param ActiveRecord $model
     * @return string
     */
    protected function getViewFile(ActiveRecord $model)
    {
        return $model->isNewRecord ? '@ylab/administer/views/create' : '@ylab/administer/views/update';
    }

    /**
     * Create title for page.
     *
     * @param ActiveRecord $model
     * @return string
     */
    protected function getTitle(ActiveRecord $model)
    {
        $class = StringHelper::basename(get_class($model));
        if ($model->isNewRecord) {
            return \Yii::t('ylab/administer', 'Create') . " $class";
        }
        return \Yii::t('ylab/administer', 'Update') . " $class #{$model->getPrimaryKey()}";
    }

    /**
     * Create breadcrumbs for page.
     *
     * @param ActiveRecord $model
     * @return array
     */
    protected function getBreadcrumbs(ActiveRecord $model)
    {
        $class = StringHelper::basename(get_class($model));
        $breadcrumbs = [];
        $breadcrumbs[] = ['label' => $class, 'url' => 'index'];
        if (!$model->isNewRecord) {
            $breadcrumbs[] = ['label' => '#' . $model->getPrimaryKey()];
            $breadcrumbs[] = \Yii::t('ylab/administer', 'Update');
        } else {
            $breadcrumbs[] = \Yii::t('ylab/administer', 'Create');
        }
        return $breadcrumbs;
    }

    /**
     * Create buttons for page.
     *
     * @param ActiveRecord $model
     * @return array
     */
    protected function getButtons(ActiveRecord $model)
    {
        $buttons['list'] = Html::a(\Yii::t('ylab/administer', 'List'), ['index'], ['class' => 'btn btn-primary']);
        if (!$model->isNewRecord) {
            $buttons['create'] = Html::a(
                \Yii::t('ylab/administer', 'Create'),
                ['create'],
                ['class' => 'btn btn-success']
            );
            $buttons['view'] = Html::a(\Yii::t('ylab/administer', 'View'), ['view'], ['class' => 'btn btn-primary']);
            $buttons['delete'] = Html::a(\Yii::t('ylab/administer', 'Delete'), ['delete'], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => \Yii::t('ylab/administer', 'Are you sure you want to delete this item?'),
                    'method' => 'POST',
                ],
            ]);
        }
        return $buttons;
    }
}
