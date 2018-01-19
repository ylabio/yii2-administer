<?php

namespace ylab\administer\components\data;

use yii\db\QueryInterface;

/**
 * Interface that describes the filtering logic.
 */
interface FilterInterface
{
    /**
     * The method that applies the passed filters to ActiveQuery.
     * @param QueryInterface $query
     * @param array $filterParams
     * @param array $filterOperators
     */
    public function addFiltersWhere(QueryInterface $query, $filterParams, $filterOperators);
}
