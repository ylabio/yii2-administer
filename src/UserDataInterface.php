<?php

namespace ylab\administer;

use yii\base\Model;

/**
 * Interface for user data.
 * This interface can be implemented in your application and specify this class in the module
 * parameter [[ylab\administer\Module::userDataClass]].
 */
interface UserDataInterface
{
    /**
     * Get user name.
     *
     * @return string
     */
    public function getUserName();

    /**
     * Get avatar path.
     *
     * @return string
     */
    public function getAvatar();

    /**
     * Get login form model.
     * 
     * @return LoginFormInterface
     */
    public function getLoginForm();
}
