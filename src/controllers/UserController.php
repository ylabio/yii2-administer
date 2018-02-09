<?php

namespace ylab\administer\controllers;

use yii\web\Controller;
use Yii;

/**
 * User controller.
 * @property \ylab\administer\Module $module
 */
class UserController extends Controller
{
    /**
     * Login action.
     *
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        $this->layout = 'login-layout';

        $model = $this->module->userData->getLoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
