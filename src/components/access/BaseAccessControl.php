<?php

namespace ylab\administer\components\access;

use yii\base\Component;
use yii\web\ForbiddenHttpException;

/**
 * Abstract class for check access to management of model.
 */
abstract class BaseAccessControl extends Component
{
    /**
     * @var string Role that has access to the module
     */
    public $defaultRole = '@';
    /**
     * @var array list of roles for models in key/value format.
     * Where `key` is a model url, and `value` is a role names that will check by [[User::can()]].
     *
     * @see \ylab\administer\Module::$modelsConfig
     * @see \yii\filters\AccessRule::$roles is example of roles
     */
    public $rules = [];

    /**
     * Checks whether the Web user is allowed access to the specified model and action.
     * If user does not have access, a [[ForbiddenHttpException]] should be thrown.
     *
     * @param string $action the ID of the action to be executed
     * @param string $modelUrl the model ID to be accessed
     * @throws ForbiddenHttpException if the user does not have access
     */
    abstract public function checkAccess($action, $modelUrl);
}