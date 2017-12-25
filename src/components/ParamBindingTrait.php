<?php

namespace ylab\administer\components;

use yii\web\NotFoundHttpException;
use ylab\administer\Module;

/**
 * Implements bind of class name to first controller action parameter.
 *
 * @property Module $module
 */
trait ParamBindingTrait
{
    /**
     * Model class config.
     * Example:
     * ```
     * [
     *     'class' => Post::class,
     *     'url' => 'posts',
     *     'labels' => ['Посты', 'Пост', 'Поста'],
     *     'menuIcon' => 'newsletter',
     * ],
     * ```
     *
     * @var array
     */
    public $modelConfig;

    /**
     * @inheritdoc
     */
    public function bindActionParams($action, $params)
    {
        $args = parent::bindActionParams($action, $params);
        if (count($args) === 0) {
            return $args;
        }

        if (!isset($this->module->modelsConfig[$args[0]])) {
            throw new NotFoundHttpException();
        }
        $this->modelConfig = $this->module->modelsConfig[$args[0]];

        $args[0] = $this->modelConfig['class'];
        return $args;
    }
}