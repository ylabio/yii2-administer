<?php

namespace ylab\administer\components\data\operators;

use yii\db\QueryInterface;

/**
 * Class LikeFilterOperator filtering operator, which performs filtering based on the equality will be determined
 * based on SQL LIKE operator.
 */
class LikeFilterOperator extends FilterOperator
{
    /**
     * @inheritdoc
     */
    public function addFilter(QueryInterface $query)
    {
        $query->andWhere(['LIKE', $this->attribute, '%' . $this->param . '%', false]);
    }
}
