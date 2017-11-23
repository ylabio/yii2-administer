<?php

namespace ylab\administer;

use yii\base\InvalidConfigException;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

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
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $urlManager = \Yii::$app->getUrlManager();
        $urlManager->addRules(
            [
                'admin' => 'admin/crud/default',
                'admin/<modelClass:[\w-]+>' => 'admin/crud/index',
                'admin/<modelClass:[\w-]+>/<action:[\w-]+>' => 'admin/crud/<action>',
                'admin/<modelClass:[\w-]+>/<action:[\w-]+>/<id:\d+>' => 'admin/crud/<action>',
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
}
