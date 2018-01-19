<?php

namespace ylab\administer\components\data;

/**
 * Class Filter A filter class that uses the new filter syntax, where the operator used is
 * Filtering is specified in the key of the associative array of filtering rules, just before the key itself.
 * Possible operators: >, <, >=, <=, !=. And also the operator%, which is placed in the value line exactly as in
 * SQL query.
 * For example:
 * <pre>
 * $deprecatedFilter->addFiltersWhere($query, ['name' => 'Вас%', '>=age' => '18']);
 * </pre>
 */
class Filter extends BaseFilter
{
    /**
     * @inheritdoc
     */
    protected function parseFilterParam($key, $value)
    {
        foreach ($this->getSupportFilterOperator() as $operator) {
            if (strpos($key, $operator) === 0) {
                return [$operator, str_replace($operator, '', $key), $value];
            }
        }
        return [self::DEFAULT_FILTER_OPERATOR, $key, $value];
    }
}
