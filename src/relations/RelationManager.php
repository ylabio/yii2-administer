<?php

namespace ylab\administer\relations;

use yii\db\ActiveRecordInterface;

class RelationManager
{
    protected $model;

    public function __construct(ActiveRecordInterface $model)
    {
        $this->model = $model;
    }
}