<?php

namespace ylab\administer\widgets;

use dmstr\widgets\Menu;
use Yii;

/**
 * {@inheritdoc}
 *
 * Переопределена логика определения активного элемента согласно логике модуля
 */
class MenuWidget extends Menu
{
    /**
     * @inheritdoc
     */
    public function isItemActive($item)
    {
        $isActive = !empty($item['url']['modelClass']) &&
            !empty(Yii::$app->controller->actionParams['modelClass']) &&
            Yii::$app->controller->actionParams['modelClass'] === $item['url']['modelClass'];

        return parent::isItemActive($item) || $isActive;
    }
}