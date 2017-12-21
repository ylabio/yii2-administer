<?php

namespace ylab\administer\components;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use ylab\administer\CrudViewBehavior;

trait FindModelTrait
{
    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $modelClass
     * @param int $id
     * @return ActiveRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($modelClass, $id)
    {
        $query = new ActiveQuery($modelClass);
        if (($model = $query->andWhere(['id' => $id])->one()) !== null) {
            $this->ensureBehavior($model);
            return $model;
        }
        throw new NotFoundHttpException();
    }

    /**
     * Check CrudViewBehavior attached, attach it if not.
     *
     * @param ActiveRecord $model
     */
    protected function ensureBehavior(ActiveRecord $model)
    {
        foreach ($model->getBehaviors() as $behavior) {
            if ($behavior instanceof CrudViewBehavior) {
                return;
            }
        }
        $model->attachBehavior('crudView', CrudViewBehavior::class);
    }
}