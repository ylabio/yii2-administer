<?php

namespace ylab\administer\helpers;

use yii\base\InvalidCallException;
use ylab\administer\buttons\AdvancedFilterButton;
use ylab\administer\buttons\CrudButton;
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
            CrudButton::TYPE_CREATE,
            AdvancedFilterButton::TYPE_ADVANCED_FILTER,
        ],
        'view' => [
            CrudButton::TYPE_INDEX,
            CrudButton::TYPE_CREATE,
            CrudButton::TYPE_UPDATE,
            CrudButton::TYPE_DELETE,
        ],
        'create' => [
            CrudButton::TYPE_INDEX,
        ],
        'update' => [
            CrudButton::TYPE_INDEX,
            CrudButton::TYPE_CREATE,
            CrudButton::TYPE_DELETE,
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
     * @return CrudButton
     */
    protected function createButton($type, $urlParams, $config)
    {
        switch ($type) {
            case CrudButton::TYPE_INDEX:
                return new IndexButton($urlParams, $config);
            case CrudButton::TYPE_VIEW:
                return new ViewButton($urlParams, $config);
            case CrudButton::TYPE_CREATE:
                return new CreateButton($urlParams, $config);
            case CrudButton::TYPE_UPDATE:
                return new UpdateButton($urlParams, $config);
            case CrudButton::TYPE_DELETE:
                return new DeleteButton($urlParams, $config);
            case AdvancedFilterButton::TYPE_ADVANCED_FILTER:
                return new AdvancedFilterButton($config);
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
