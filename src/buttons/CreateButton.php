<?php

namespace ylab\administer\buttons;

/**
 * Class for button referenced to create action.
 */
class CreateButton extends AbstractButton
{
    /**
     * @inheritdoc
     */
    public $text = 'Create';
    /**
     * @inheritdoc
     */
    public $options = ['class' => 'btn btn-success btn-right btn-flat glyphicon-plus'];
    /**
     * @inheritdoc
     */
    public $action = 'create';
}
