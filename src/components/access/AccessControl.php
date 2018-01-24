<?php

namespace ylab\administer\components\access;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

/**
 * Default implementation of access checking.
 *
 * @inheritdoc
 */
class AccessControl extends BaseAccessControl
{
    /**
     * @inheritdoc
     */
    public function checkAccess($action, $modelUrl)
    {
        $roles = (array)ArrayHelper::getValue($this->rules, $modelUrl, []);

        if (!empty($roles)) {

            foreach ($roles as $role) {
                if (Yii::$app->user->can($role)) {
                    return;
                }
            }

            throw new ForbiddenHttpException(\Yii::t('ylab/administer', 'Access denied'));
        }
    }
}