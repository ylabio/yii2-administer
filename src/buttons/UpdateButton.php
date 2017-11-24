<?php

namespace ylab\administer\buttons;

/**
 * Class for button referenced to update action.
 */
class UpdateButton extends AbstractButton
{
    /**
     * @inheritdoc
     */
    public $text = 'Update';
    /**
     * @inheritdoc
     */
    public $options = ['class' => 'btn btn-success btn-flat glyphicon-pencil'];
    /**
     * @inheritdoc
     */
    public $action = 'update';
}
