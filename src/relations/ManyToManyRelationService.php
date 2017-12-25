<?php

namespace ylab\administer\relations;

use yii\db\ActiveQueryInterface;

/**
 * Service for manage many-to-many relation type.
 */
class ManyToManyRelationService
{
    /**
     * Definition that this service can be used.
     *
     * @param DataStorage $storage
     * @return bool
     */
    public function isNeed(DataStorage $storage)
    {
        $relation = $storage->getRelationObject();

        if ($relation->multiple && !empty($relation->via) && !($relation->via instanceof ActiveQueryInterface)) {
            return true;
        }

        return false;
    }

    /**
     * @param DataStorage $storage
     */
    public function save(DataStorage $storage)
    {
        $storage->unlinkRemovedData(true);
        $storage->linkNewData();
    }
}