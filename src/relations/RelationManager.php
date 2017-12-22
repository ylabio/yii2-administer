<?php

namespace ylab\administer\relations;

use yii\base\ModelEvent;
use yii\db\ActiveRecord;

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
     * @var DataStorage[]
     */
    protected $storage = [];

    /**
     * @param ActiveRecord $model
     * @param array $relations
     */
    public function __construct(ActiveRecord $model, array $relations)
    {
        $this->model = $model;
        $this->services = [
            \Yii::createObject(ManyToManyRelationService::class),
        ];
        foreach ($relations as $relationName) {
            $this->storage[] = \Yii::createObject(DataStorage::class, [$relationName, $this->model]);
        }
    }

    /**
 * Process owner-model before save event.
 *
 * @param ModelEvent $event object of event called by model
 */
    public function beforeSave($event)
    {
        $isValid = $event->isValid;
        // проверить что данные полученные для связи существуют
        foreach ($this->storage as $relationName => $storage) {
            $storage->loadExisted();
            if (!$storage->validate()) {
                $isValid = false;
            }
        }
        $event->isValid = $isValid;
    }

    /**
     * Process owner-model after save event. Save models.
     */
    public function afterSave()
    {
        foreach ($this->storage as $relationName => $storage) {
            foreach ($this->services as $service) {
                if ($service->isNeed($storage)) {
                    $service->save($storage);
                }
            }
        }
    }

    /**
     * Permission for this behavior to set relational attributes.
     *
     * {@inheritdoc}
     */
    public function canSetProperty($name)
    {
        foreach ($this->storage as $storage) {
            if ($storage->canSetRawData($name)) {
                return true;
            }
        }
        return false;
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
        foreach ($this->storage as $storage) {
            if ($storage->canSetRawData($name)) {
                $storage->setRawData((array) $value);
                return true;
            }
        }
        return false;
    }

    /**
     * Prepare to delete related models.
     */
    public function beforeDelete()
    {
        foreach ($this->storage as $relationName => $storage) {
            $storage->setRawData([]); // reset received
        }
    }

    /**
     * Delete related models.
     */
    public function afterDelete()
    {
        foreach ($this->storage as $relationName => $storage) {
            foreach ($this->services as $service) {
                if ($service->isNeed($storage)) {
                    $service->save($storage);
                }
            }
        }
    }
}