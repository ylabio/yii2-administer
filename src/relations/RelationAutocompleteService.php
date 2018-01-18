<?php

namespace ylab\administer\relations;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use ylab\administer\AutocompleteServiceInterface;

/**
 * Service of getting hints for relational autocomplete fields.
 */
class RelationAutocompleteService implements AutocompleteServiceInterface
{
    /**
     * @var ActiveRecord
     */
    protected $model;

    /**
     * @param ActiveRecord $model
     */
    public function __construct(ActiveRecord $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritdoc
     */
    public function getHints($relation, $keyAttribute, $labelAttribute, $q, $limit = 10)
    {
        $relationQuery = $this->model->getRelation($relation);
        $query = $this->buildQuery($relationQuery, $keyAttribute, $labelAttribute, $q);

        if (is_a($query, Query::class)) {
            return $query->limit($limit)->all();
        }

        return [];
    }

    /**
     * Create a query for retrieve hints.
     *
     * @param ActiveQuery $relation
     * @param string $keyAttribute Attribute in related model uses for key
     * @param string $labelAttribute Attribute in related model uses for label
     * @param string $q Query text
     * @return Query|null
     */
    protected function buildQuery(ActiveQuery $relation, $keyAttribute, $labelAttribute, $q)
    {
        if (!$relation) {
            return null;
        }

        $query = new ActiveQuery($relation->modelClass);

        return $query
            ->select([$keyAttribute, $labelAttribute])
            ->andWhere(['like', $labelAttribute, $q]);
    }
}