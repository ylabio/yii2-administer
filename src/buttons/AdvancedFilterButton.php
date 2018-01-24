<?php

namespace ylab\administer\buttons;

use yii\helpers\Html;

/**
 * Class for advanced filter button.
 */
class AdvancedFilterButton extends Button
{
    /**
     * @var string
     */
    const TYPE_ADVANCED_FILTER = 'advancedFilter';
    
    /**
     * @inheritdoc
     */
    public $text = 'Filters';
    /**
     * @inheritdoc
     */
    public $options = ['class' => 'btn btn-success btn-flat filter-toggle'];

    /**
     * @inheritdoc
     */
    public function render()
    {
        return Html::button($this->getText(), $this->getOptions());
    }
}
