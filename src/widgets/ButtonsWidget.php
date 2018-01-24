<?php

namespace ylab\administer\widgets;

use yii\base\Widget;
use ylab\administer\buttons\Button;

/**
 * Class for rendering buttons.
 */
class ButtonsWidget extends Widget
{
    /**
     * @var Button[]
     */
    public $buttons = [];

    /**
     * @inheritdoc
     */
    public function run()
    {
        $out = '';
        foreach ($this->buttons as $button) {
            $out .= $button->render();
        }
        return $out;
    }
}
