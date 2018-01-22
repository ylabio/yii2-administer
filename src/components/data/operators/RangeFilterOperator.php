<?php


namespace ylab\administer\components\data\operators;

use yii\db\QueryInterface;
use yii\helpers\ArrayHelper;

/**
 * It divides the incoming parameter [[param]] into two values, and then creates a filtering condition that will search for
 * records greater than or equal to the first value and smaller than the second value.
 */
class RangeFilterOperator extends FilterOperator
{
    /**
     * @var string Value separator for [[param]].
     */
    public $delimiter = ' - ';

    /**
     * @inheritdoc
     */
    public function addFilter(QueryInterface $query)
    {
        $params = $this->parseParam($this->param);

        $query->andWhere(['>=', $this->attribute, ArrayHelper::getValue($params, 0, '')]);
        $query->andWhere(['<', $this->attribute, ArrayHelper::getValue($params, 1, '')]);
    }

    /**
     * @param string $param
     */
    private function parseParam($param)
    {
        return explode($this->delimiter, $param);
    }
}
