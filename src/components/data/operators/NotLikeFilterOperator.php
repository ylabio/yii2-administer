<?php


namespace ylab\administer\components\data\operators;

use yii\db\QueryInterface;

/**
 * Class NotLikeFilterOperator filtering operator, performing filtering based on not like of attribute
 * Filtering and filtering value.
 */
class NotLikeFilterOperator extends FilterOperator
{
    /**
     * @inheritdoc
     */
    public function addFilter(QueryInterface $query)
    {
        $query->andWhere(['not like', $this->attribute, '%' . $this->param . '%', false]);
    }
}
