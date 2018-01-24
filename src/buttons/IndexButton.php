<?php

namespace ylab\administer\buttons;

/**
 * Class for button referenced to index action.
 */
class IndexButton extends CrudButton
{
    /**
     * @inheritdoc
     */
    public $text = 'Index';
    /**
     * @inheritdoc
     */
    public $options = ['class' => 'btn btn-primary btn-right btn-flat glyphicon-list'];
    /**
     * @inheritdoc
     */
    public $action = 'index';
}
