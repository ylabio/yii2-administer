<?php

namespace ylab\administer\relations;

use yii\base\InvalidConfigException;
use yii\base\ModelEvent;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class for manage relational data.
 */
class RelationManager
{
    /**
     * @var ActiveRecord
     */
    protected $model;
    /**
     * @var array List of services for saving each type of relation.
     */
    protected $services = [];
    /**
     * @var array Definition of relations.
     */
    protected $relations = [];
    /**
     * @var array Relation attributes list.
     */
    protected $relationalFields = [];
    /**
     * @var RelatedData[] Relation attributes data.
     */
    protected $relationalData = [];
    /**
     * @var bool Indices finish of all saving operations.
     */
    protected $relationalFinished = false;

    /**
     * @param ActiveRecord $model
     * @param array $relations
     */
    public function __construct(ActiveRecord $model, array $relations)
    {
        $this->model = $model;
        $this->relations = $relations;
        $this->initRelationalFields();
        $this->services = [
            \Yii::createObject(ManyToManyRelationService::class),
        ];
    }

    /**
     * Initialize fields.
     *
     * @throws InvalidConfigException
     */
    protected function initRelationalFields()
    {
        if ($this->relations) {
            $keys = array_keys($this->relations);
            $values = array_values($this->relations);
            foreach ($values as $index => $value) {
                if (is_string($value)) {
                    $this->relationalFields[$value] = [];
                } else if (is_callable($value)) {
                    $this->relationalFields[$keys[$index]] = $value;
                } else {
                    throw new InvalidConfigException(
                        sprintf(
                            'Element at index "%s", must be string or callable, %s given.', $index, gettype($value)
                        )
                    );
                }
            }
        }
    }

    /**
 * Process owner-model before save event.
 *
 * @param ModelEvent $event object of event called by model
 */
    public function beforeSave($event)
    {
        $this->loadData();
        $event->isValid = $this->validateData();
    }
    /**
     * Return saving state of relation data finished or not. It's will finished after all relations models will saved.
     * @return bool
     */
    public function isRelationalFinished()
    {
        return $this->relationalFinished;
    }

    /**
     * Process owner-model after save event. Save models.
     */
    public function afterSave()
    {
        $this->saveData();
    }

    /**
     * Permission for this behavior to set relational attributes.
     *
     * {@inheritdoc}
     */
    public function canSetProperty($name)
    {
        return array_key_exists($name, $this->relationalFields);
    }

    /**
     * Setter for data.
     *
     * @param string $name
     * @param string $value
     * @return bool
     */
    public function setRelationValue($name, $value)
    {
        if ($this->canSetProperty($name)) {
//            $this->relationalData[$name] = ['data' => $value];
            $this->setRelationalData($name, $value);
            return true;
        }
        return false;
    }

    protected function setRelationalData($name, $value)
    {
        if (!array_key_exists($name, $this->relationalData)) {
            $this->relationalData[$name] = new RelatedData($name);
        }
        $this->relationalData[$name]->setData($value);
    }

    /**
     * Load relational data from owner-model getter.
     *
     * - Create related ActiveRecord objects from POST array data.
     * - Load existing related ActiveRecord objects from database.
     * - Check ON condition format.
     * - Get ActiveQuery object from attribute getter method.
     *
     * Fill $this->relationalData array for each relational attribute:
     *
     * ```php
     * $this->relationalData[$attribute] = [
     *      'newModels' => ActiveRecord[],
     *      'oldModels' => ActiveRecord[],
     *      'activeQuery' => ActiveQuery,
     * ];
     * ```
     *
     * @throws RelationException
     */
    protected function loadData()
    {
        /** @var ActiveQuery $activeQuery */
        foreach ($this->relationalData as $attribute => $data) {

//            $getter = 'get' . ucfirst($attribute);
//            $data['activeQuery'] = $activeQuery = $this->model->$getter();
            $data->setActiveQuery($this->model->getRelation($attribute));
//            $data['newModels'] = [];
//            $data['oldModels'] = [];
//            $data['newRows'] = [];
//            $data['oldRows'] = [];

            if (!$this->validateOnCondition($data->getActiveQuery())) {
                \Yii::$app->getDb()->getTransaction()->rollBack();
                throw new \ErrorException('ON condition for attribute ' . $attribute . ' must be associative array');
            }

            foreach ($this->services as $service) {
                if ($service->isNeedLoad($data)) {
                    $service->load(
                        $data,
                        $this->relations[$attribute],
                        $this->model
                    );
                }
            }

//            if ($activeQuery->multiple) {
//                if (empty($activeQuery->via)) { // one-to-many
//                    $this->loadModelsOneToMany($attribute);
//                } else { // many-to-many
//                    if ($activeQuery->via instanceof ActiveQueryInterface) { // viaTable
//                        $this->loadModelsManyToManyViaTable($attribute);
//                    } else { // via
//                        $this->loadModelsManyToManyVia($attribute);
//                    }
//                }
//
//            } elseif (!empty($data['data'])) {
//                // one-to-one
//                $this->loadModelsOneToOne($attribute);
//            }

//            if (empty($activeQuery->via)) {
//                $data['oldModels'] = $activeQuery->all();
//            }
            $data->setData(null);

            $data->replaceExistingModels();
        }
    }

    /**
     * Validate relational models, return true only if all models successfully validated. Skip errors for foreign
     * columns.
     *
     * @return bool
     */
    protected function validateData()
    {
        foreach ($this->relationalData as $attribute => $data) {
            /** @var ActiveRecord $model */
            /** @var ActiveQuery $activeQuery */
            $activeQuery = $data->getActiveQuery();
            foreach ($data->getNewModels() as $model) {
                if (!$model->validate()) {
                    $_errors = $model->getErrors();
                    $errors = [];

                    foreach ($_errors as $relatedAttribute => $error) {
                        if (!$activeQuery->multiple || !isset($activeQuery->link[$relatedAttribute])) {
                            $errors[$relatedAttribute] = $error;
                        }
                    }

                    if (count($errors)) {
                        $this->model->addError($attribute, $errors);

                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Save changed related models.
     *
     * - Delete old related models, which not exist in POST array.
     * - Create new related models, which not exist in database.
     * - Update owner models for one-to-one relation.
     */
    public function saveData()
    {
        $needSaveOwner = false;

        foreach ($this->relationalData as $attribute => $data) {
            // delete models
            $this->deleteModels($attribute);
            // save models
            $this->saveModels($attribute);

            if (!$data->getActiveQuery()->multiple && (count($data->getNewModels()) == 0 || !$data->getNewModels()[0]->isNewRecord)) {
                $needSaveOwner = true;
                foreach ($data->getActiveQuery()->link as $childAttribute => $parentAttribute) {
                    $this->model->$parentAttribute = count($data->getNewModels()) ? $data->getNewModels()[0]->$childAttribute : null;
                }
            }
        }

        $this->relationalFinished = true;

        if ($needSaveOwner) {
            $model = $this->model;
//            $this->detach();
            if (!$model->save()) {
                \Yii::$app->getDb()->getTransaction()->rollBack();
                throw new \ErrorException('Owner-model ' . $model::className() . ' not saved due to unknown error');
            }
        }
    }

    /**
     * Execute callback for each relation
     *
     * - if error occurred throws exception
     *
     * @param array $relations
     * @param callable $callback
     * @throws RelationException
     */
    protected function relationsMap($relations, $callback)
    {
        try {
            if (is_callable($callback)) {
                array_map($callback, $relations);
            }
        } catch (\Exception $e) {
            \Yii::$app->getDb()->getTransaction()->rollBack();
            throw new \ErrorException('Owner-model not saved due to unknown error');
        }
    }

    /**
     * Check existing row if it found in old rows
     *
     * @param $row
     * @param $attribute
     * @return mixed
     */
    protected function isExistingRow($row, $attribute)
    {
        $rowAttributes = $row;
        unset($rowAttributes[$this->relationalData[$attribute]->getJunctionColumn()]);

        foreach ($this->relationalData[$attribute]->getOldRows() as $oldRow) {
            $oldModelAttributes = $oldRow;
            unset($oldModelAttributes[$this->relationalData[$attribute]->getJunctionColumn()]);
            if ($oldModelAttributes == $rowAttributes) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if model was deleted (not found in new models).
     *
     * @param ActiveRecord $model
     * @param $attribute
     *
     * @return bool
     */
    protected function isDeletedModel($model, $attribute)
    {
        $modelAttributes = $model->attributes;
        unset($modelAttributes[$model->primaryKey()[0]]);

        foreach ($this->relationalData[$attribute]->getNewModels() as $newModel) {
            /** @var ActiveRecord $newModel */
            $newModelAttributes = $newModel->attributes;
            unset($newModelAttributes[$newModel->primaryKey()[0]]);

            if ($newModelAttributes == $modelAttributes) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if row was deleted (not found in new rows).
     *
     * @param $row
     * @param $attribute
     * @return bool
     */
    protected function isDeletedRow($row, $attribute)
    {
        $rowAttribute = $row;
        unset($rowAttribute[$this->relationalData[$attribute]->getJunctionColumn()]);

        foreach ($this->relationalData[$attribute]->getNewRows() as $newRow) {
            $newRowAttributes = $newRow;
            unset($newRowAttributes[$this->relationalData[$attribute]->getJunctionColumn()]);
            if ($newRowAttributes == $rowAttribute) {
                return false;
            }
        }

        return true;
    }

    /**
     * Delete related models. Rollback transaction and throw RelationException, if error occurred while deleting.
     */
    public function afterDelete()
    {
        foreach ($this->relationalFields as $attribute => $value) {
//            $getter = 'get' . ucfirst($attribute);
            /** @var ActiveQuery $activeQuery */
            $activeQuery = $this->model->getRelation($attribute);//$getter();

            $models = [];
            if (empty($activeQuery->via)) {
                $models = $activeQuery->all();
            } else {
                if ($activeQuery->via instanceof ActiveQueryInterface) { // viaTable

                    $junctionTable = $activeQuery->via->from[0];
                    list($junctionColumn) = array_keys($activeQuery->via->link);
                    list($relatedColumn) = array_values($activeQuery->link);

                    $rows = (new Query())
                        ->from($junctionTable)
                        ->select([
                            $junctionColumn,
                            $relatedColumn
                        ])
                        ->where([
                            $junctionColumn => $this->model->getPrimaryKey(),
                        ])->all();

                    $this->relationsMap($rows, function($row) use ($junctionTable) {
                        \Yii::$app->db->createCommand()
                            ->delete($junctionTable, $row)
                            ->execute();
                    });

                } else { // via
                    $junctionGetter = 'get' . ucfirst($activeQuery->via[0]);
                    $models = $this->model->$junctionGetter()->all();
                }
            }

            foreach ($models as $model) {
                if (!$model->delete()) {
                    \Yii::$app->getDb()->getTransaction()->rollBack();
                    throw new \ErrorException('Model ' . $model::className() . ' not deleted due to unknown error');
                }
            }
        }
    }

    /**
     * Validate ON condition in ActiveQuery
     *
     * @param ActiveQuery $activeQuery
     * @return bool
     */
    protected function validateOnCondition($activeQuery)
    {
        if (
            !ArrayHelper::isAssociative($activeQuery->on) &&
            !empty($activeQuery->on)
        ) {
            return false;
        }

        if (
            $activeQuery->multiple &&
            !empty($activeQuery->via) &&
            is_array($activeQuery->via) &&
            is_object($activeQuery->via[1]) &&
            !ArrayHelper::isAssociative($activeQuery->via[1]->on) &&
            !empty($activeQuery->via[1]->on)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Load new model from POST for one-to-one relation
     *
     * @param $attribute
     */
    protected function loadModelsOneToOne($attribute)
    {
        $data = $this->relationalData[$attribute];

        $activeQuery = $data['activeQuery'];
        $class = $activeQuery->modelClass;

        $model = new $class($data['data']);
        if (isset($this->relations[$attribute]) && is_callable($this->relations[$attribute])) {
            $model = call_user_func($this->relations[$attribute], $model, $data['data']);
        }
        $data['newModels'][] = $model;

        $this->relationalData[$attribute] = $data;
    }

    /**
     * Load new models from POST for one-to-many relation
     *
     * @param $attribute
     */
    protected function loadModelsOneToMany($attribute)
    {
        $data = $this->relationalData[$attribute];

        $activeQuery = $data['activeQuery'];
        $class = $activeQuery->modelClass;

        // default query conditions
        $params = !ArrayHelper::isAssociative($activeQuery->on) ? [] : $activeQuery->on;
        // one-to-many
        foreach ($activeQuery->link as $childAttribute => $parentAttribute) {
            $params[$childAttribute] = $this->model->$parentAttribute;
        }

        if (!empty($data['data'])) {
            foreach ($data['data'] as $attributes) {
                $model = new $class(
                    array_merge(
                        $params,
                        ArrayHelper::isAssociative($attributes) ? $attributes : []
                    )
                );
                if (isset($this->relations[$attribute]) && is_callable($this->relations[$attribute])) {
                    $model = call_user_func($this->relations[$attribute], $model, $attributes);
                }
                $data['newModels'][] = $model;
            }
        }

        $this->relationalData[$attribute] = $data;
    }

    /**
     * Load new models from POST for many-to-many relation with viaTable
     *
     * @param $attribute
     * @throws RelationException
     */
    protected function loadModelsManyToManyViaTable($attribute)
    {
        $data = $this->relationalData[$attribute];

        $activeQuery = $data['activeQuery'];
        /** @var ActiveRecord $class */
        $class = $activeQuery->modelClass;

        $via = $activeQuery->via;
        $data['junctionTable'] = $via->from[0];

        list($data['junctionColumn']) = array_keys($via->link);
        list($data['relatedColumn']) = array_values($activeQuery->link);
        $junctionColumn = $data['junctionColumn'];
        $relatedColumn = $data['relatedColumn'];

        if (!empty($data['data'])) {
            // make sure what all row's ids from POST exists in database
            $countManyToManyModels = $class::find()->where([$class::primaryKey()[0] => $data['data']])->count();
            if ($countManyToManyModels != count($data['data'])) {
                throw new \ErrorException('Related records for attribute ' . $attribute . ' not found');
            }
            // create new junction rows
            foreach ($data['data'] as $relatedModelId) {
                $junctionModel = array_merge(
                    !ArrayHelper::isAssociative($via->on) ? [] : $via->on,
                    [$junctionColumn => $this->model->getPrimaryKey()]
                );
                $junctionModel[$relatedColumn] = $relatedModelId;
                if (isset($this->relations[$attribute]) && is_callable($this->relations[$attribute])) {
                    $junctionModel = call_user_func($this->relations[$attribute], $junctionModel, $relatedModelId);
                }
                $data['newRows'][] = $junctionModel;
            }
        }

        if (!empty($this->model->getPrimaryKey())) {
            $data['oldRows'] = (new Query())
                ->from($data['junctionTable'])
                ->select(array_merge(
                        [$junctionColumn, $relatedColumn],
                        !ArrayHelper::isAssociative($via->on) ? [] : array_keys($via->on))
                )->where(array_merge(
                    !ArrayHelper::isAssociative($via->on) ? [] : $via->on,
                    [$junctionColumn => $this->model->getPrimaryKey()]
                ))->all();
        }

        $this->relationalData[$attribute] = $data;
    }

    /**
     * Save all new models for attribute
     *
     * @param $attribute
     * @throws RelationException
     */
    protected function saveModels($attribute)
    {
        $data = $this->relationalData[$attribute];

        /** @var ActiveRecord $model */
        foreach ($data->getNewModels() as $model) {
            if ($model->isNewRecord) {

                foreach ($this->services as $service) {
                    if ($service->isNeedSave($data)) {
                        $service->save($model, $data, $this->model);
                    }
                }

//                if (!empty($data['activeQuery']->via)) {
//                    // only for many-to-many
//                    $junctionColumn = $data['junctionColumn'];
//                    $model->$junctionColumn = $this->model->getPrimaryKey();
//                } elseif ($data['activeQuery']->multiple) {
//                    // only one-to-many
//                    foreach ($data['activeQuery']->link as $childAttribute => $parentAttribute) {
//                        $model->$childAttribute = $this->model->$parentAttribute;
//                    }
//                }
//                if (!$model->save()) {
//                    \Yii::$app->getDb()->getTransaction()->rollBack();
//                    throw new \ErrorException('Model ' . $model::className() . ' not saved due to unknown error');
//                }
            }
        }

        // only for many-to-many
        $this->relationsMap($data->getNewRows(), function($row) use ($attribute, $data) {
            $junctionColumn = $data->getJunctionColumn();
            $row[$junctionColumn] = $this->model->getPrimaryKey();
            if (!$this->isExistingRow($row, $attribute)) {
                \Yii::$app->db->createCommand()
                    ->insert($data->getJunctionTable(), $row)
                    ->execute();
            }
        });
    }

    /**
     * Delete all old models for attribute if it needed
     *
     * @param $attribute
     * @throws RelationException
     */
    protected function deleteModels($attribute)
    {
        $data = $this->relationalData[$attribute];

        /** @var ActiveRecord $model */
        foreach ($data->getOldModels() as $model) {
            if ($this->isDeletedModel($model, $attribute)) {
                if (!$model->delete()) {
                    \Yii::$app->getDb()->getTransaction()->rollBack();
                    throw new \ErrorException('Model ' . $model::className() . ' not deleted due to unknown error');
                }
            }
        }

        $this->relationsMap($data->getOldRows(), function($row) use ($attribute, $data) {
            if ($this->isDeletedRow($row, $attribute)) {
                \Yii::$app->db->createCommand()
                    ->delete($data->getJunctionTable(), $row)
                    ->execute();
            }
        });
    }
}