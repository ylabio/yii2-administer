<?php

namespace ylab\administer\buttons;

use Yii;

/**
 * Button class which contain default button logic.
 */
abstract class Button
{
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
     * Button constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        Yii::configure($this, $config);
    }

    /**
     * Get button text.
     *
     * @return string
     */
    protected function getText()
    {
        if ($this->text !== null) {
            return Yii::t('ylab/administer', $this->text);
        }
        return '';
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

    /**
     * Render button.
     *
     * @return string
     */
    abstract public function render();
}
