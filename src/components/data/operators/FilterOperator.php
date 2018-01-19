<?php


namespace ylab\administer\components\data\operators;

use yii\base\BaseObject;
use yii\db\QueryInterface;

/**
 * Class FilterOperator abstract class of the filtering operator. This class contains properties that specify
 * The attribute and the value of which will be filtered. To create your own filtering statement
 * It is necessary to inherit this class and define the method `addFilter`, in which to describe the logic of the
 * filter.
 */
abstract class FilterOperator extends BaseObject
{
    /**
     * @var string The name of the attribute against which you want to filter.
     */
    public $attribute = null;

    /**
     * @var mixed The meaning of which will be filtered.
     */
    public $param = null;

    /**
     * Applies the current filtering operator to QueryInterface.
     * @param QueryInterface $query
     */
    abstract public function addFilter(QueryInterface $query);
}
