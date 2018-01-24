<?php

namespace ylab\administer\buttons;

/**
 * Class for button referenced to view action.
 */
class ViewButton extends CrudButton
{
    /**
     * @inheritdoc
     */
    public $text = 'View';
    /**
     * @inheritdoc
     */
    public $options = ['class' => 'btn btn-primary btn-flat glyphicon-eye-open'];
    /**
     * @inheritdoc
     */
    public $action = 'view';
}
