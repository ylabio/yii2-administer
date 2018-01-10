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
     * @param string $attribute
     * @param string|array $value
     * @param string $separator
     */
    public function addDateInterval($attribute, $value, $separator = ' - ')
    {
        $value = $this->prepareDateIntervalValue($value, $separator);
        $this->query->andFilterWhere([
            'and',
            ['>=', $attribute, $value[0]],
            ['<=', $attribute, $value[1]],
        ]);
    }

    /**
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