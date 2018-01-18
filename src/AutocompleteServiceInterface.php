<?php

namespace ylab\administer;

/**
 * Interface for autocomplete services.
 */
interface AutocompleteServiceInterface
{
    /**
     * Returns list of hints.
     *
     * @param string $relation Name of relation in model
     * @param string $keyAttribute Attribute in related model uses for key
     * @param string $labelAttribute Attribute in related model uses for label
     * @param string $q Query text
     * @param int $limit
     * @return array
     */
    public function getHints($relation, $keyAttribute, $labelAttribute, $q, $limit = 10);
}