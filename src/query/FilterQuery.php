<?php

namespace ylab\administer\query;

use yii\db\ActiveQuery;

/**
 * Class is decorator for using custom filters.
 */
class FilterQuery
{
    /**
     * @var ActiveQuery
     */
    private $query;

    /**
     * @param ActiveQuery $query
     */
    public function __construct(ActiveQuery $query)
    {
        $this->query = $query;
    }

    /**
     * Adds to the query the selection from the interval.
     *
     * @param string $attribute
     * @param string|array $value
     * @param string $separator
     */
    public function addInterval($attribute, $value, $separator = ' - ')
    {
        $value = $this->prepareDateIntervalValue($value, $separator);
        $this->query->andFilterWhere([
            'and',
            ['>=', $attribute, $value[0]],
            ['<=', $attribute, $value[1]],
        ]);
    }

    /**
     * Converts a string with a date interval to an array.
     *
     * @param string|array $value
     * @param string $separator
     * @return array
     */
    private function prepareDateIntervalValue($value, $separator)
    {
        if (is_string($value)) {
            $value = explode(empty($separator) ? ' - ' : $separator, $value, 2);
        }

        return $value;
    }
}