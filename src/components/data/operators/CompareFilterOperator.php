<?php


namespace ylab\administer\components\data\operators;

use yii\base\InvalidConfigException;
use yii\db\QueryInterface;

/**
 * Class CompareFilterOperator is a filtering operator that performs filtering based on a comparison of the filter attribute
 * and values using the operator specified in the `CompareFilterOperator::$operator` property.
 */
class CompareFilterOperator extends FilterOperator
{
    /**
     * @var string The operator that will be used when comparing the attribute with the filter value.
     */
    public $operator = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->operator === null) {
            throw new InvalidConfigException('The "operator" property must be set.');
        }
    }

    /**
     * @inheritdoc
     */
    public function addFilter(QueryInterface $query)
    {
        $query->andWhere([$this->operator, $this->attribute, $this->param]);
    }
}
