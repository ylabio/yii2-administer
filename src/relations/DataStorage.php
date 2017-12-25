<?php

namespace ylab\administer\relations;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class represent of repository for relational data.
 */
class DataStorage
{
    /**
     * @var string Name of relation in model.
     */
    protected $relationName;
    /**
     * @var ActiveRecord
     */
    protected $model;
    /**
     * @var \yii\db\ActiveQuery|\yii\db\ActiveQueryInterface Relation object.
     */
    protected $relation;
    /**
     * @var array Received data.
     */
    protected $rawData = [];
    /**
     * @var array Existed data by relation.
     */
    protected $existedData = [];
    /**
     * @var bool Data was received.
     */
    protected $received = false;

    /**
     * @param string $relationName
     * @param ActiveRecord $model
     */
    public function __construct($relationName, ActiveRecord $model)
    {
        $this->relationName = $relationName;
        $this->model = $model;
        $this->relation = $this->model->getRelation($this->relationName);
    }

    /**
     * Check ability to work with data by relation name.
     * @param string $relationName
     * @return bool
     */
    public function canSetRawData($relationName)
    {
        return $relationName === $this->relationName;
    }

    /**
     * Load received data.
     * @param array $value
     */
    public function setRawData(array $value)
    {
        $this->rawData = array_filter($value);
        $this->received = true;
    }

    /**
     * Load existed data.
     */
    public function loadExisted()
    {
        $key = $this->getForeignKey();
        // try to get data
        $existedData = $this->model->{$this->relationName};

        if ($existedData) {
            $this->existedData = array_values(ArrayHelper::getColumn($existedData, $key));
        }
    }

    /**
     * Returns added data.
     * Is diff between received data and already existing.
     * This data will be saved.
     * @return array
     */
    public function getNewData()
    {
        return array_diff($this->rawData, $this->existedData);
    }

    /**
     * Returns removed data.
     * Is diff between already existing data and received.
     * This data will be deleted.
     * @return array
     */
    public function getRemovedData()
    {
        return array_diff($this->existedData, $this->rawData);
    }

    /**
     * Perform validation of data for adding.
     * @return bool
     */
    public function validate()
    {
        $newDataCount = count($this->getNewData());

        if ($newDataCount < 1) {
            return true;
        }

        $key = $this->getForeignKey();
        $modelClass = $this->getRelationObject()->modelClass;
        // checking that all new related entity is existing
        $existInDbCount = $modelClass::find()->where([$key => $this->getNewData()])->count();

        return $existInDbCount == $newDataCount;
    }

    /**
     * Returns name of relation.
     * @return string
     */
    public function getRelationName()
    {
        return $this->relationName;
    }

    /**
     * Returns object of relation.
     * @return \yii\db\ActiveQuery|\yii\db\ActiveQueryInterface
     */
    public function getRelationObject()
    {
        return $this->relation;
    }

    /**
     * Returns key of related entity.
     * @return string
     */
    protected function getForeignKey()
    {
        $relation = $this->relation;
        $foreignKeys = array_keys($relation->link);
        return array_shift($foreignKeys);
    }

    /**
     * Delete relation with removed data.
     * @param bool $delete
     */
    public function unlinkRemovedData($delete = false)
    {
        $modelClass = $this->getRelationObject()->modelClass;

        if (!$modelClass) {
            return;
        }

        foreach ($this->getRemovedData() as $id) {
            unset($model);
            $model = $modelClass::findOne($id);
            if ($model) {
                $this->model->unlink($this->getRelationName(), $model, $delete);
            }
        }
    }

    /**
     * Create relation with a new received data.
     * @param array $extraColumns
     */
    public function linkNewData($extraColumns = [])
    {
        $modelClass = $this->getRelationObject()->modelClass;

        if (!$modelClass) {
            return;
        }

        foreach ($this->getNewData() as $id) {
            unset($model);
            $model = $modelClass::findOne($id);
            if ($model) {
                $this->model->link($this->getRelationName(), $model, $extraColumns);
            }
        }
    }

    /**
     * @return boolean
     */
    public function needSave()
    {
        return $this->received;
    }
}