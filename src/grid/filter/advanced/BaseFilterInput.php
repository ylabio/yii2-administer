<?php

namespace ylab\administer\grid\filter\advanced;

/**
 * Basic class for advanced filters in GridView.
 */
abstract class BaseFilterInput
{
    /**
     * @var string Name of the filter parameter.
     */
    public $filterParam = 'filter';

    /**
     * @var string Name of the attribute.
     */
    public $attribute;

    /**
     * @var string Model URL.
     */
    public $modelUrl;

    /**
     * Get the Attribute parameter as an array.
     *
     * @return string
     */
    public function getAttribute()
    {
        return $this->filterParam . '[' . $this->attribute . ']';
    }
    
    /**
     * Returns filter as string.
     *
     * @param array $options
     * @return string
     */
    abstract public function render(array $options = []);
}
