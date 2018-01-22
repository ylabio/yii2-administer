<?php

namespace ylab\administer\components\data;

use ylab\administer\components\data\operators\FilterOperator;
use ylab\administer\components\data\operators\CompareFilterOperator;
use ylab\administer\components\data\operators\EqualFilterOperator;
use ylab\administer\components\data\operators\LikeFilterOperator;
use ylab\administer\components\data\operators\NotEqualFilterOperator;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\db\QueryInterface;
use ylab\administer\components\data\operators\NotLikeFilterOperator;
use ylab\administer\components\data\operators\RangeFilterOperator;

/**
 * Class BaseFilter abstract class of filtering. This class encompasses the whole logic of working with classes
 * filtration operators. To create your own filter class, you must inherit this class and define a method
 * `parseFilterParam`. This method must match one key-value pair from an associative array
 * filter rules the type of the filter operator, the attribute and the value by which the filtering will be performed.
 * Types of filtering operators are specified in the AbstractRestFilter::$filterOperators property, as an associative
 * array, where:
 * -key is the type of the filter operator. there is a constant of `BaseFilter::DEFAULT_FILTER_OPERATOR`
 * The defining type of filtering operator used by default
 * -String string|array configuration to create the class of the filter operator
 */
abstract class BaseFilter extends BaseObject implements FilterInterface
{
    const DEFAULT_FILTER_OPERATOR = '=';

    /**
     * @var FilterOperator[]
     * The configuration for creating filtering operators
     */
    public $filterOperators = [
        self::DEFAULT_FILTER_OPERATOR => [
            'class' => EqualFilterOperator::class,
        ],
        '~' => [
            'class' => LikeFilterOperator::class,
        ],
        '!~' => [
            'class' => NotLikeFilterOperator::class,
        ],
        '>=' => [
            'class' => CompareFilterOperator::class,
            'operator' => '>=',
        ],
        '<=' => [
            'class' => CompareFilterOperator::class,
            'operator' => '<=',
        ],
        '>' => [
            'class' => CompareFilterOperator::class,
            'operator' => '>',
        ],
        '<' => [
            'class' => CompareFilterOperator::class,
            'operator' => '<',
        ],
        '!=' => [
            'class' => NotEqualFilterOperator::class,
        ],
        'range' => [
            'class' => RangeFilterOperator::class,
        ],
    ];

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        foreach ($this->filterOperators as &$filterOperator) {
            $filterOperator = \Yii::createObject($filterOperator);
            if (!$filterOperator instanceof FilterOperator) {
                throw new InvalidConfigException('Неверно задано поле "filterOperators".');
            }
        }
    }

    /**
     * The method is the return operator for filtering depending on the type passed
     * @param string $type string type of operator for filtering
     * @param string $attribute  the name of the attribute by which to filter
     * @param mixed $param the value of which is filtered
     * @return FilterOperator
     */
    protected function getFilterOperator($type, $attribute, $param)
    {
        $operator = $this->filterOperators[$type];
        $operator->attribute = $attribute;
        $operator->param = $param;

        return $operator;
    }

    /**
     * List of supported types of operators for filtering
     * @return string[]
     */
    protected function getSupportFilterOperator()
    {
        return array_keys($this->filterOperators);
    }

    /**
     * The method that applies the passed filters to ActiveQuery
     * @param QueryInterface $query
     * @param array $filterParams
     */
    public function addFiltersWhere(QueryInterface $query, $filterParams, $filterOperators)
    {
        foreach ($filterParams as $key => $value) {
            if ($value === '') {
                continue;
            }
            if (array_key_exists($key, $filterOperators)) {
                $key = $filterOperators[$key] . $key;
            }
            list($type, $attribute, $param) = $this->parseFilterParam($key, $value);
            $this->getFilterOperator($type, $attribute, $param)
                ->addFilter($query);
        }
    }

    /**
     * The method in which you need to parse the transferred key-value of an associative array of filter rules
     * and return the corresponding type of filtering, the attribute for filtering and the value to be generated
     * filtering.
     * @param $key string associative array of filter rules
     * @param $value mixed value of an associative array of filter rules
     * @return array array of the form [$type, $attribute, $param], where:
     * $type - filtration type
     * $attribute - filtration attribute
     * $param - the value to filter against
     */
    abstract protected function parseFilterParam($key, $value);
}
