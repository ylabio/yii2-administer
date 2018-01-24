<?php

namespace ylab\administer\buttons;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * CrudButton class which contain default button logic for CRUD.
 */
class CrudButton extends Button
{
    /**
     * @var string
     */
    const TYPE_INDEX = 'index';
    /**
     * @var string
     */
    const TYPE_VIEW = 'view';
    /**
     * @var string
     */
    const TYPE_CREATE = 'create';
    /**
     * @var string
     */
    const TYPE_UPDATE = 'update';
    /**
     * @var string
     */
    const TYPE_DELETE = 'delete';

    /**
     * Button url action.
     *
     * @var string
     */
    public $action;

    /**
     * Button url query params.
     *
     * @var array
     */
    protected $urlParams;

    /**
     * CrudButton constructor.
     * @param array $urlParams
     * @param array $config
     */
    public function __construct(array $urlParams, array $config)
    {
        $this->urlParams = $urlParams;
        parent::__construct($config);
    }

    /**
     * Render button.
     *
     * @return string
     */
    public function render()
    {
        return Html::a(
            $this->getText(),
            $this->getUrl(),
            $this->getOptions()
        );
    }

    /**
     * Get button url.
     *
     * @return array
     */
    protected function getUrl()
    {
        return ArrayHelper::merge([$this->action], $this->urlParams);
    }
}
