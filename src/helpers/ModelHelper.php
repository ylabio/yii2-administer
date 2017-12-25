<?php

namespace ylab\administer\helpers;

use yii\db\ActiveRecord;
use ylab\administer\CrudViewBehavior;

/**
 * Helper class for uses of entity models.
 */
class ModelHelper
{
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
}