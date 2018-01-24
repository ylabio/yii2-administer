<?php

namespace ylab\administer\buttons;

use Yii;

/**
 * Class for button referenced to delete action.
 */
class DeleteButton extends CrudButton
{
    /**
     * @inheritdoc
     */
    public $text = 'Delete';
    /**
     * @inheritdoc
     */
    public $options = [
        'class' => 'btn btn-danger btn-right btn-flat glyphicon-trash',
    ];
    /**
     * @inheritdoc
     */
    public $action = 'delete';

    /**
     * @inheritdoc
     */
    protected function getOptions()
    {
        if (!isset($this->options['data'])) {
            $this->options['data'] = [
                'confirm' => Yii::t('ylab/administer', 'Are you sure you want to delete this item?'),
                'method' => 'POST',
            ];
        }
        return parent::getOptions();
    }
}
