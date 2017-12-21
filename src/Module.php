<?php

namespace ylab\administer;

use yii\base\InvalidConfigException;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\i18n\PhpMessageSource;

/**
 * @inheritdoc
 */
class Module extends \yii\base\Module
{
    /**
     * @var array
     *
     * Example:
     * ```
     * [
     *     Post::class,
     *     [
     *         'class' => Tag::class,
     *         'url' => 'post-tags',
     *         'labels' => ['Теги', 'Тег', 'Тега'],
     *         'menuIcon' => 'dashboard',
     *     ],
     * ]
     * ```
     */
    public $modelsConfig = [];
    /**
     * Url prefix for module actions.
     *
     * @var string
     */
    public $urlPrefix = 'admin';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->registerTranslations();
        $urlManager = \Yii::$app->getUrlManager();
        $urlManager->addRules(
            [
                "$this->urlPrefix" => "$this->id/crud/default",
                "$this->urlPrefix/<modelClass:[\\w-]+>" => "$this->id/crud/index",
                "$this->urlPrefix/<modelClass:[\\w-]+>/<action:(autocomplete)>/<id:\\d+>" => "$this->id/api/<action>",
                "$this->urlPrefix/<modelClass:[\\w-]+>/<action:[\\w-]+>" => "$this->id/crud/<action>",
                "$this->urlPrefix/<modelClass:[\\w-]+>/<action:[\\w-]+>/<id:\\d+>" => "$this->id/crud/<action>",
            ]
        );
        $this->normalizeModelsConfig();
    }

    /**
     * Get menu items for `Menu` widget.
     *
     * @return array
     */
    public function getMenuItems()
    {
        $items = [];
        foreach ($this->modelsConfig as $config) {
            $items[] = [
                'label' => $config['labels'][0],
                'icon' => $config['menuIcon'],
                'url' => ['index', 'modelClass' => $config['url']],
            ];
        }
        return $items;
    }

    /**
     * Make config valid, set unspecified fields to default values.
     *
     * @throws InvalidConfigException
     */
    protected function normalizeModelsConfig()
    {
        $config = [];
        foreach ($this->modelsConfig as $modelConfig) {
            if (is_string($modelConfig)) {
                $modelConfig = ['class' => $modelConfig];
            } elseif (!isset($modelConfig['class'])) {
                throw new InvalidConfigException('Each modelConfig item must contain "class" field.');
            }
            $url = isset($modelConfig['url'])
                ? $modelConfig['url']
                : Inflector::camel2id(StringHelper::basename($modelConfig['class']));
            $labels = [];
            foreach ([true, false, false] as $pos => $plurals) {
                $labels[$pos] = isset($modelConfig['labels'][$pos])
                    ? $modelConfig['labels'][$pos]
                    : ($plurals
                        ? Inflector::pluralize(StringHelper::basename($modelConfig['class']))
                        : StringHelper::basename($modelConfig['class'])
                    );
            }
            $config[$url] = [
                'class' => $modelConfig['class'],
                'labels' => $labels,
                'menuIcon' => isset($modelConfig['menuIcon']) ? $modelConfig['menuIcon'] : 'dashboard',
                'url' => $url,
            ];
        }
        $this->modelsConfig = $config;
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
}
