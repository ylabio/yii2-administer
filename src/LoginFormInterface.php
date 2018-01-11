<?php

namespace ylab\administer;

use yii\base\Model;

/**
 * Interface for login form.
 * This interface can be implemented in your application and return an object of this class
 * in the method [[ylab\administer\UserDataInterface::getLoginForm()]].
 */
interface LoginFormInterface
{
    /**
     * Login user.
     *
     * @return boolean
     */
    public function login();

    /**
     * Get login attribute.
     *
     * @return string
     */
    public function getLoginAttribute();

    /**
     * Get password attribute.
     *
     * @return string
     */
    public function getPasswordAttribute();

    /**
     * Get remember me attribute.
     *
     * @return string
     */
    public function getRememberMeAttribute();
}
