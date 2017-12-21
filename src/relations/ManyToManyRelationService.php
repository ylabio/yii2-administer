<?php

namespace ylab\administer\relations;

use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Service for manage many-to-many relation type.
 */
class ManyToManyRelationService
{
    /**
     * Definition that this service can be used for load.
     *
     * @param ActiveQuery $activeQuery
     * @return bool
     */
    public function isNeedLoad(ActiveQuery $activeQuery)
    {
        if ($activeQuery->multiple && !empty($activeQuery->via) && !($activeQuery->via instanceof ActiveQueryInterface)) {
            return true;
        }
        return false;
    }

    /**
     * Perform loading data for relation.
     *
     * @param string $attribute
     * @param array $data
     * @param string|\Closure $relation
     * @param ActiveRecord $model
     * @return array
     * @throws \ErrorException
     */
    public function load($attribute, array $data, $relation, ActiveRecord $model)
    {
        $activeQuery = $data['activeQuery'];
        /** @var ActiveRecord $class */
        $class = $activeQuery->modelClass;

        if (!is_object($activeQuery->via[1])) {
            throw new \ErrorException('via condition for attribute ' . $attribute . ' cannot must be object');
        }

        $via = $activeQuery->via[1];
        $junctionGetter = 'get' . ucfirst($activeQuery->via[0]);
        /** @var ActiveRecord $junctionModelClass */
        $data['junctionModelClass'] = $junctionModelClass = $via->modelClass;
        $data['junctionTable'] = $junctionModelClass::tableName();

        list($data['junctionColumn']) = array_keys($via->link);
        list($data['relatedColumn']) = array_values($activeQuery->link);
        $junctionColumn = $data['junctionColumn'];
        $relatedColumn = $data['relatedColumn'];

        if (!empty($data['data'])) {
            // make sure what all model's ids from POST exists in database
            $countManyToManyModels = $class::find()->where([$class::primaryKey()[0] => $data['data']])->count();
            if ($countManyToManyModels != count($data['data'])) {
                throw new \ErrorException('Related records for attribute ' . $attribute . ' not found');
            }
            // create new junction models
            foreach ($data['data'] as $relatedModelId) {
                $junctionModel = new $junctionModelClass(
                    array_merge(
                        !ArrayHelper::isAssociative($via->on) ? [] : $via->on,
                        [$junctionColumn => $model->getPrimaryKey()]
                    )
                );
                $junctionModel->$relatedColumn = $relatedModelId;
                if ($relation && is_callable($relation)) {
                    $junctionModel = call_user_func($relation, $junctionModel, $relatedModelId);
                }
                $data['newModels'][] = $junctionModel;
            }
        }

        $data['oldModels'] = $model->$junctionGetter()->all();

        return $data;
    }

    /**
     * Definition that this service can be used for save.
     *
     * @param ActiveQuery $activeQuery
     * @return bool
     */
    public function isNeedSave(ActiveQuery $activeQuery)
    {
        return !empty($activeQuery->via);
    }

    /**
     * Perform saving data for relation.
     *
     * @param ActiveRecord $model
     * @param array $data
     * @param ActiveRecord $owner
     * @throws \ErrorException
     */
    public function save(ActiveRecord $model, array $data, ActiveRecord $owner)
    {
        $junctionColumn = $data['junctionColumn'];
        $model->$junctionColumn = $owner->getPrimaryKey();
        if (!$model->save()) {
            \Yii::$app->getDb()->getTransaction()->rollBack();
            throw new \ErrorException('Model ' . $model::className() . ' not saved due to unknown error');
        }
    }
}