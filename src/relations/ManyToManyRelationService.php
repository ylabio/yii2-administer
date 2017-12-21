<?php

namespace ylab\administer\relations;

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
     * @param RelatedData $data
     * @return bool
     */
    public function isNeedLoad(RelatedData $data)
    {
        $activeQuery = $data->getActiveQuery();
        if ($activeQuery->multiple && !empty($activeQuery->via) && !($activeQuery->via instanceof ActiveQueryInterface)) {
            return true;
        }
        return false;
    }

    /**
     * Perform loading data for relation.
     *
     * @param RelatedData $data
     * @param string|\Closure $relation
     * @param ActiveRecord $model
     * @throws \ErrorException
     */
    public function load(RelatedData $data, $relation, ActiveRecord $model)
    {
        $attribute = $data->getAttribute();
        $activeQuery = $data->getActiveQuery();

        if (!is_object($activeQuery->via[1])) {
            throw new \ErrorException('via condition for attribute ' . $attribute . ' cannot must be object');
        }

        $via = $activeQuery->via[1];
        /** @var ActiveRecord $junctionModelClass */
        $junctionModelClass = $via->modelClass;
        $data->setJunctionTable($junctionModelClass::tableName());

        list($junctionColumn) = array_keys($via->link);
        $data->setJunctionColumn($junctionColumn);
        list($relatedColumn) = array_values($activeQuery->link);

        if (!empty($data->getData())) {
            // make sure what all model's ids from POST exists in database
            if ($data->getCountModels() != count($data->getData())) {
                throw new \ErrorException('Related records for attribute ' . $attribute . ' not found');
            }
            // create new junction models
            foreach ((array)$data->getData() as $relatedModelId) {
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
                $data->pushNewModel($junctionModel);
            }
        }

        $data->setOldModels($model->getRelation($activeQuery->via[0])->all());
    }

    /**
     * Definition that this service can be used for save.
     *
     * @param RelatedData $data
     * @return bool
     */
    public function isNeedSave(RelatedData $data)
    {
        return !empty($data->getActiveQuery()->via);
    }

    /**
     * Perform saving data for relation.
     *
     * @param ActiveRecord $model
     * @param RelatedData $data
     * @param ActiveRecord $owner
     * @throws \ErrorException
     */
    public function save(ActiveRecord $model, RelatedData $data, ActiveRecord $owner)
    {
        $junctionColumn = $data->getJunctionColumn();
        $model->$junctionColumn = $owner->getPrimaryKey();
        if (!$model->save()) {
            \Yii::$app->getDb()->getTransaction()->rollBack();
            throw new \ErrorException('Model ' . $model::className() . ' not saved due to unknown error');
        }
    }
}