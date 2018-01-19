<?php

namespace ylab\administer\components\data;

use yii\db\ActiveQuery;

/**
 * Specifies methods for obtaining a field configuration for filtering and receiving a query.
 * Must be implemented in a derived class from [[yii\base\Model]]
 */
interface FilterModelInterface
{
    /**
     * Returns the configuration for the filter fields.
     * @return array
     */
    public function filters();

    /**
     * Returns the ActiveQuery.
     * @return ActiveQuery
     */
    public function getQuery();
}
