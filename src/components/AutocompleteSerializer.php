<?php

namespace ylab\administer\components;

use yii\rest\Serializer;

/**
 * Representation of results for relational autocomplete widget.
 *
 * @inheritdoc
 */
class AutocompleteSerializer extends Serializer
{
    /**
     * @inheritdoc
     */
    public $collectionEnvelope = 'results';

    /**
     * @inheritdoc
     */
    protected function serializeModel($model)
    {
        if ($this->request->getIsHead()) {
            return null;
        }

        list($fields, $expand) = $this->getRequestedFields();
        return $model->toArray(['id', 'text'], $expand);
    }
}