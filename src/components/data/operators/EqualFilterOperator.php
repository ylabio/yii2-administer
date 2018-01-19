<?php

namespace ylab\administer\components\data\operators;

use yii\db\QueryInterface;

/**
 * Class EqualFilterOperator filtering operator, which performs filtering based on the equality of the filter attribute
 * and the value of the filter.
 */
class EqualFilterOperator extends FilterOperator
{
    /**
     * @inheritdoc
     */
    public function addFilter(QueryInterface $query)
    {
        $query->andWhere([$this->attribute => $this->param]);
    }
}
