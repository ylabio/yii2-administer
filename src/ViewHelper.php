<?php

namespace ylab\administer;

use yii\base\InvalidCallException;
use ylab\administer\buttons\AbstractButton;
use ylab\administer\buttons\CreateButton;
use ylab\administer\buttons\DeleteButton;
use ylab\administer\buttons\IndexButton;
use ylab\administer\buttons\UpdateButton;
use ylab\administer\buttons\ViewButton;

/**
 * Helper class for get additional page elements.
 */
class ViewHelper
{
    /**
     * Create breadcrumbs items config.
     *
     * @param string $action
     * @param null|string $url
     * @param null|string $name
     * @param null|int $id
     * @return array
     */
    public function getBreadcrumbs($action, $url = null, $name = null, $id = null)
    {
        if ($action === 'index') {
            return [$name];
        }

        $breadcrumbs = [];
        $breadcrumbs[] = ['label' => $name, 'url' => ['index', 'modelClass' => $url]];
        if ($action === 'create') {
            $breadcrumbs[] = \Yii::t('ylab/administer', 'Create');
        } else {
            if ($action === 'update') {
                $breadcrumbs[] = [
                    'label' => "#$id",
                    'url' => ['view', 'modelClass' => $url, 'id' => $id],
                ];
                $breadcrumbs[] = \Yii::t('ylab/administer', ucwords($action));
            } else {
                $breadcrumbs[] = "#$id";
            }
        }

        return $breadcrumbs;
    }

    /**
     * Create actions buttons.
     *
     * @param string $action
     * @param string $modelClass
     * @param null|int $id
     * @param array $buttonsConfig
     * @return array
     */
    public function getButtons($action, $modelClass, $id = null, $buttonsConfig = [])
    {
        switch ($action) {
            case 'create':
                return [
                    $this->createButton(
                        AbstractButton::TYPE_INDEX,
                        ['modelClass' => $modelClass],
                        $this->getButtonConfig(AbstractButton::TYPE_INDEX, $buttonsConfig)

                    ),
                ];
            case 'update':
                return [
                    $this->createButton(
                        AbstractButton::TYPE_INDEX,
                        ['modelClass' => $modelClass],
                        $this->getButtonConfig(AbstractButton::TYPE_INDEX, $buttonsConfig)

                    ),
                    $this->createButton(
                        AbstractButton::TYPE_CREATE,
                        ['modelClass' => $modelClass],
                        $this->getButtonConfig(AbstractButton::TYPE_CREATE, $buttonsConfig)

                    ),
                    $this->createButton(
                        AbstractButton::TYPE_VIEW,
                        ['modelClass' => $modelClass, 'id' => $id],
                        $this->getButtonConfig(AbstractButton::TYPE_VIEW, $buttonsConfig)

                    ),
                    $this->createButton(
                        AbstractButton::TYPE_DELETE,
                        ['modelClass' => $modelClass, 'id' => $id],
                        $this->getButtonConfig(AbstractButton::TYPE_DELETE, $buttonsConfig)

                    ),
                ];
            case 'view':
                return [
                    $this->createButton(
                        AbstractButton::TYPE_INDEX,
                        ['modelClass' => $modelClass],
                        $this->getButtonConfig(AbstractButton::TYPE_INDEX, $buttonsConfig)

                    ),
                    $this->createButton(
                        AbstractButton::TYPE_CREATE,
                        ['modelClass' => $modelClass],
                        $this->getButtonConfig(AbstractButton::TYPE_CREATE, $buttonsConfig)

                    ),
                    $this->createButton(
                        AbstractButton::TYPE_UPDATE,
                        ['modelClass' => $modelClass, 'id' => $id],
                        $this->getButtonConfig(AbstractButton::TYPE_UPDATE, $buttonsConfig)

                    ),
                    $this->createButton(
                        AbstractButton::TYPE_DELETE,
                        ['modelClass' => $modelClass, 'id' => $id],
                        $this->getButtonConfig(AbstractButton::TYPE_DELETE, $buttonsConfig)

                    ),
                ];
            case 'index':
            default:
                return [
                    $this->createButton(
                        AbstractButton::TYPE_CREATE,
                        ['modelClass' => $modelClass],
                        $this->getButtonConfig(AbstractButton::TYPE_CREATE, $buttonsConfig)
                    ),
                ];
        }
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
