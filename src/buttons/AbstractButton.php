<?php

namespace ylab\administer\buttons;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * AbstractButton class which contain default button logic.
 */
class AbstractButton
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
     * Button text
     *
     * @var string
     */
    public $text;
    /**
     * Button HTML options.
     *
     * @var array
     */
    public $options = [];
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
     * AbstractButton constructor.
     * @param array $urlParams
     * @param array $config
     */
    public function __construct(array $urlParams, array $config)
    {
        $this->urlParams = $urlParams;
        \Yii::configure($this, $config);
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
     * Get button text.
     *
     * @return string
     */
    protected function getText()
    {
        if ($this->text !== null) {
            return \Yii::t('ylab/administer', $this->text);
        }
        return '';
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

    /**
     * Get button HTML options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return $this->options;
    }
}
