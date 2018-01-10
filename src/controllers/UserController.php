<?php

namespace ylab\administer\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use Yii;

/**
 * User controller.
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
        $this->layout = '@ylab/administer/views/login-layout';

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

    /**
     * @inheritdoc
     */
    public function getViewPath()
    {
        return \Yii::getAlias('@ylab/administer/views');
    }
}
