<?php

namespace ylab\administer\helpers;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use ylab\administer\CrudViewBehavior;

/**
 * Helper class for uses of entity models.
 */
class ModelHelper
{
    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $className
     * @param string|int $id
     * @return ActiveRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public static function findModel($className, $id)
    {
        $query = new ActiveQuery($className);

        if (($model = $query->andWhere(['id' => $id])->one()) !== null) {
            self::ensureCrudViewBehavior($model);
            return $model;
        }

        throw new NotFoundHttpException();
    }

    /**
     * Check CrudViewBehavior attached, attach it if not.
     *
     * @param ActiveRecord $model
     */
    public static function ensureCrudViewBehavior(ActiveRecord $model)
    {
        foreach ($model->getBehaviors() as $behavior) {
            if ($behavior instanceof CrudViewBehavior) {
                return;
            }
        }
        $model->attachBehavior('crudView', CrudViewBehavior::class);
    }

    /**
     * Creation a new model with attached CrudViewBehavior.
     *
     * @param string $className
     * @return ActiveRecord the created model
     */
    public static function createModel($className)
    {
        $model = new $className();
        ModelHelper::ensureCrudViewBehavior($model);
        return $model;
    }
}