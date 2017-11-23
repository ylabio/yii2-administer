<?php

namespace ylab\administer;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\i18n\PhpMessageSource;
use yii\validators\EmailValidator;
use yii\validators\FileValidator;
use yii\validators\ImageValidator;
use yii\validators\NumberValidator;

/**
 * Behavior for add to ActiveRecord models possibility to use in Administer CRUD module.
 * Usage:
 * ```
 * public function behaviors()
 * {
 *     return [
 *         'crudView' => [
 *             'class' => CrudViewBehavior::class,
 *             'formRenderer' => [
 *                  'attributesInputs' => [
 *                      'name',
 *                      'avatar' => [
 *                          'type' => 'image',
 *                      ],
 *                      'doc' => [
 *                          'type' => 'file',
 *                      ],
 *                  ],
 *              ],
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
     * @inheritdoc
     */
    public function attach($owner)
    {
        parent::attach($owner);
        $this->registerTranslations();

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
    public function render()
    {
        return $this->formRenderer->render($this->owner, $this->getFieldsConfig());
    }

    /**
     * Register needed i18n files
     */
    protected function registerTranslations()
    {
        if (!isset(\Yii::$app->i18n->translations['ylab/administer'])
            && !isset(\Yii::$app->i18n->translations['ylab/administer/*'])
        ) {
            \Yii::$app->i18n->translations['ylab/administer'] = [
                'class' => PhpMessageSource::class,
                'basePath' => '@ylab/administer/messages',
                'forceTranslation' => true,
                'fileMap' => [
                    'ylab/administer' => 'administer.php',
                ],
            ];
        }
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
     * @param $type
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
