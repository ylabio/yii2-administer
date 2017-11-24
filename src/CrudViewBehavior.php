<?php

namespace ylab\administer;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\validators\EmailValidator;
use yii\validators\FileValidator;
use yii\validators\ImageValidator;
use yii\validators\NumberValidator;
use ylab\administer\helpers\BreadcrumbsHelper;
use ylab\administer\helpers\ButtonsHelper;

/**
 * Behavior for add to ActiveRecord models possibility to use in Administer CRUD module.
 * Example usage:
 * ```
 * public function behaviors()
 * {
 *     return [
 *         'crudView' => [
 *             'class' => CrudViewBehavior::class,
 *             'formRenderer' => [
 *                 'attributesInputs' => [
 *                     'name',
 *                     'avatar' => [
 *                         'type' => 'image',
 *                     ],
 *                     'doc' => [
 *                         'type' => 'file',
 *                     ],
 *                 ],
 *             ],
 *            'buttonsConfig' => [
 *                AbstractButton::TYPE_CREATE => [
 *                    'text' => 'Add Post',
 *                    'options' => [
 *                        'class' => 'btn btn-danger',
 *                    ],
 *                ],
 *            ],
 *         ],
 *     ];
 * }
 * ```
 *
 * {@inheritdoc}
 * @property ActiveRecord $owner
 */
class CrudViewBehavior extends Behavior
{
    /**
     * @var FormRenderer
     */
    public $formRenderer;
    /**
     * @var array
     */
    public $buttonsConfig = [];

    /**
     * @var ButtonsHelper
     */
    protected $buttonsHelper;
    /**
     * @var BreadcrumbsHelper
     */
    protected $breadcrumbsHelper;

    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        parent::attach($owner);
        $this->buttonsHelper = \Yii::createObject(ButtonsHelper::class);
        $this->breadcrumbsHelper = \Yii::createObject(BreadcrumbsHelper::class);

        if ($this->formRenderer === null) {
            $formRenderer = FormRenderer::class;
        } else {
            if (is_array($this->formRenderer) && !isset($this->formRenderer['class'])) {
                $this->formRenderer['class'] = FormRenderer::class;
            }
            $formRenderer = $this->formRenderer;
        }
        $this->formRenderer = \Yii::createObject($formRenderer);
    }

    /**
     * Render form and return it as string.
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function renderForm()
    {
        return $this->formRenderer->renderForm($this->owner, $this->getFieldsConfig());
    }

    /**
     * Get actions buttons.
     *
     * @param string $action
     * @param string $modelClass
     * @param null|int $id
     * @return array
     */
    public function getButtons($action, $modelClass, $id = null)
    {
        return $this->buttonsHelper->getButtons($action, $modelClass, $id, $this->buttonsConfig);
    }

    /**
     * Get breadcrumbs items config.
     *
     * @param string $action
     * @param null|string $url
     * @param null|string $name
     * @param null|int $id
     * @return array
     */
    public function getBreadcrumbs($action, $url = null, $name = null, $id = null)
    {
        return $this->breadcrumbsHelper->getBreadcrumbs($action, $url, $name, $id);
    }

    /**
     * Get fields config based on `rules()` model method.
     *
     * @return array
     */
    protected function getFieldsConfig()
    {
        $config = [];
        foreach ($this->owner->getActiveValidators() as $validator) {
            $class = get_class($validator);
            switch ($class) {
                case EmailValidator::class:
                    $type = FormField::TYPE_EMAIL;
                    break;
                case ImageValidator::class:
                    $type = FormField::TYPE_IMAGE;
                    break;
                case FileValidator::class:
                    $type = FormField::TYPE_FILE;
                    break;
                case NumberValidator::class:
                    $type = FormField::TYPE_NUMBER;
                    break;
                default:
                    continue 2;
            }
            $config = ArrayHelper::merge($config, $this->addInConfig($type, $validator->getAttributeNames()));
        }

        foreach ($this->owner->attributes() as $attribute) {
            if (!isset($config[$attribute])) {
                $config[$attribute] = ['type' => FormField::TYPE_STRING];
            }
        }

        return $config;
    }

    /**
     * Create config for scope of attributes.
     *
     * @param string $type
     * @param array $attributes
     * @return array
     */
    private function addInConfig($type, array $attributes)
    {
        $config = [];
        foreach ($attributes as $attribute) {
            $config[$attribute] = ['type' => $type];
        }
        return $config;
    }
}
