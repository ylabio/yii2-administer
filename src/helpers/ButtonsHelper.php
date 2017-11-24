<?php

namespace ylab\administer\helpers;

use yii\base\InvalidCallException;
use ylab\administer\buttons\AbstractButton;
use ylab\administer\buttons\CreateButton;
use ylab\administer\buttons\DeleteButton;
use ylab\administer\buttons\IndexButton;
use ylab\administer\buttons\UpdateButton;
use ylab\administer\buttons\ViewButton;

/**
 * Helper class for generating actions buttons.
 */
class ButtonsHelper
{
    /**
     * Map as action => button types.
     *
     * @var array
     */
    protected static $buttonsMap = [
        'index' => [
            AbstractButton::TYPE_CREATE,
        ],
        'view' => [
            AbstractButton::TYPE_INDEX,
            AbstractButton::TYPE_CREATE,
            AbstractButton::TYPE_UPDATE,
            AbstractButton::TYPE_DELETE,
        ],
        'create' => [
            AbstractButton::TYPE_INDEX,
        ],
        'update' => [
            AbstractButton::TYPE_INDEX,
            AbstractButton::TYPE_CREATE,
            AbstractButton::TYPE_VIEW,
            AbstractButton::TYPE_DELETE,
        ],
    ];

    /**
     * Create actions buttons.
     *
     * @param string $action
     * @param string $modelClass
     * @param null|int $id
     * @param array $buttonsConfig
     * @return array
     * @throws InvalidCallException
     */
    public function getButtons($action, $modelClass, $id = null, $buttonsConfig = [])
    {
        if (!isset(static::$buttonsMap[$action])) {
            throw new InvalidCallException("For action '$action' there are no buttons.");
        }
        $buttons = [];
        foreach (static::$buttonsMap[$action] as $type) {
            $buttons[] = $this->createButton(
                $type,
                ['modelClass' => $modelClass, 'id' => $id],
                $this->getButtonConfig($type, $buttonsConfig)
            );
        }
        return $buttons;
    }

    /**
     * Create button based on type.
     *
     * @param string $type
     * @param array $urlParams
     * @param array $config
     * @return AbstractButton
     */
    protected function createButton($type, $urlParams, $config)
    {
        switch ($type) {
            case AbstractButton::TYPE_INDEX:
                return new IndexButton($urlParams, $config);
            case AbstractButton::TYPE_VIEW:
                return new ViewButton($urlParams, $config);
            case AbstractButton::TYPE_CREATE:
                return new CreateButton($urlParams, $config);
            case AbstractButton::TYPE_UPDATE:
                return new UpdateButton($urlParams, $config);
            case AbstractButton::TYPE_DELETE:
                return new DeleteButton($urlParams, $config);
            default:
                throw new InvalidCallException("Undefined button type: $type.");
        }
    }

    /**
     * Get config for concrete button.
     *
     * @param string $type
     * @param array $config
     * @return array
     */
    protected function getButtonConfig($type, array $config)
    {
        return isset($config[$type]) ? $config[$type] : [];
    }
}
