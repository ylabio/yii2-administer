<?php

namespace ylab\administer\components\data;

use Yii;
use yii\data\ActiveDataProvider as BaseActiveDataProvider;

/**
 * Expansion of the standard ActiveDataProvider, which makes it possible to apply filtering using
 * Filter FilterInterface.
 */
class ActiveDataProvider extends BaseActiveDataProvider
{
    /**
     * @var FilterInterface Class used for filtration.
     */
    public $requestFilter = Filter::class;

    /**
     * @var array The additional operators that can be passed to this array are:
     *
     * ```
     * [
     *     'in' => EqualTaxonomyFilterOperator::class,
     *     ...
     * ]
     * ```
     */
    public $customFilterOperators = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        /** @var FilterInterface $requestFilter */
        $requestFilter = Yii::createObject($this->requestFilter);
        foreach ($this->customFilterOperators as $operator => $customFilterOperator) {
            $requestFilter->filterOperators[$operator] = Yii::createObject($customFilterOperator);
        }
        parent::init();
        $filters = Yii::$app->request->getQueryParam('filter', []);
        $operators = Yii::$app->request->getQueryParam('operator', []);
        $requestFilter->addFiltersWhere($this->query, $filters, $operators);
    }
}
