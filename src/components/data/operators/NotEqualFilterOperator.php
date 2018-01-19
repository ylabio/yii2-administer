<?php


namespace ylab\administer\components\data\operators;

use yii\db\QueryInterface;

/**
 * Class NotEqualFilterOperator filtering operator, performing filtering based on non-equality of attribute
 * Filtering and filtering value.
 */
class NotEqualFilterOperator extends FilterOperator
{
    /**
     * @inheritdoc
     */
    public function addFilter(QueryInterface $query)
    {
        switch (true) {
            case is_array($this->param):
                $query->andWhere(['not in', $this->attribute, $this->param]);
                break;
            case is_null($this->param):
                $query->andWhere(['not', [$this->attribute => null]]);
                break;
            default:
                $query->andWhere(['<>', $this->attribute, $this->param]);
        }
    }
}
