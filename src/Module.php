<?php

namespace ylab\administer;

use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\i18n\PhpMessageSource;
use Yii;
use ylab\administer\components\access\AccessControl;
use ylab\administer\components\access\BaseAccessControl;
use ylab\administer\relations\AutocompleteService;

/**
 * {@inheritdoc}
 * @property UserDataInterface $userData
 */
class Module extends \yii\base\Module
{
    /**
     * @var array
     *
     * Example:
     * ```
     * [
     *     Post::class,
     *     [
     *         'class' => Tag::class,
     *         'url' => 'post-tags',
     *         'labels' => ['Теги', 'Тег', 'Тега'],
     *         'menuIcon' => 'dashboard',
     *     ],
     * ]
     * ```
     */
    public $modelsConfig = [];

    /**
     * Url prefix for module actions.
     *
     * @var string
     */
    public $urlPrefix = 'admin';

    /**
     * Base url for uploads.
     *
     * @var string
     */
    public $uploadsUrl = '/uploads/';

    /**
     * Base path for uploads.
     *
     * @var string
     */
    public $uploadsPath = '@webroot' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;

    /**
     * Class that implements the [[UserDataInterface]].
     *
     * @var string|array
     */
    public $userDataClass;

    /**
     * @var array
     *
     * Example:
     * ```
     * [
     *     'options' => [
     *         'class' => 'sidebar-menu',
     *     ],
     *     'items' => [
     *         [
     *             'modelId' => 'post',
     *         ],
     *         [
     *             'label' => 'Справочники',
     *             'icon' => 'list',
     *             'items' => [
     *                 ['modelId' => 'post-tags'],
     *             ],
     *         ],
     *     ]
     * ]
     * ```
     */
    public $menuConfig = [];

    /**
     * @var array
     *
     * Example:
     * ```php
     * [
     *     'defaultRole' => 'admin',
     *     'rules' => [
     *         'post' => ['contentManager', 'admin'],
     *         'post-tags' => [],
     *     ],
     * ]
     * ```
     */
    public $access = [];

    /**
     * @var BaseAccessControl
     */
    protected $accessControl;

    /**
     * @var UserDataInterface
     */
    private $userData;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->registerDI();
        $this->registerTranslations();
        Yii::$app->user->loginUrl = [$this->id . '/user/login'];
        $this->configureUrlManager();
        $this->normalizeModelsConfig();
        $this->configureAccessControl();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        if ($this->getBehavior('access') !== null) {
            return parent::behaviors();
        }

        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'controllers' => ['admin/user'],
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'controllers' => ['admin/api', 'admin/crud'],
                        'allow' => true,
                        'roles' => [$this->getAccessControl()->defaultRole],
                    ],
                ],
            ]
        ]);
    }

    /**
     * Get menu items for `Menu` widget.
     *
     * @return array
     * @throws InvalidConfigException
     */
    public function getMenuItems()
    {
        return $this->getMenuItem(ArrayHelper::getValue($this->menuConfig, 'items', []));
    }

    /**
     * Get configuration for list of menu items.
     *
     * @param array $config
     * @return array Configuration for `Menu` widget
     * @throws InvalidConfigException
     */
    protected function getMenuItem($config = [])
    {
        $items = [];
        foreach ($config as $element) {
            $label = ArrayHelper::getValue($element, 'label');
            $icon = ArrayHelper::getValue($element, 'icon');
            $url = ArrayHelper::getValue($element, 'url');
            $visible = true;

            // getting values from configuration of model
            if (isset($element['modelId'])) {
                $modelConfig = ArrayHelper::getValue($this->modelsConfig, $element['modelId']);

                if (!$modelConfig) {
                    throw new InvalidConfigException("Model with ID \"{$element['modelId']}\" is not configured.");
                }

                $label = $label ?: $modelConfig['labels'][0];
                $icon = $icon ?: $modelConfig['menuIcon'];
                $url = $url ?: ['index', 'modelClass' => $modelConfig['url']];
                try {
                    $this->getAccessControl()->checkAccess('index', $modelConfig['url']);
                } catch (Exception $e) {
                    $visible = false;
                }
            }

            $subitems = isset($element['items']) ? $this->getMenuItem($element['items']) : null;

            if ($subitems !== null) {
                // checking count of visible subitems
                $visibleSubitems = array_filter($subitems, [$this, 'filterVisibleMenuItems']);
                if (count($visibleSubitems) < 1) {
                    // disable visibility of item if it has not visible subitems
                    $visible = false;
                }
            }

            $items[] = [
                'label' => $label,
                'icon' => $icon,
                'url' => $url ?: '#',
                'items' => $subitems,
                'visible' => $visible,
            ];
        }
        return $items;
    }

    /**
     * Filters visible menu items.
     *
     * @param array $item
     * @return bool
     */
    protected function filterVisibleMenuItems($item)
    {
        return is_array($item) && !empty($item) && $item['visible'];
    }

    /**
     * Get options from `$menuConfig` for the `Menu` widget.
     *
     * @return array
     */
    public function getMenuOptions()
    {
        return ArrayHelper::getValue($this->menuConfig, 'options', []);
    }

    /**
     * Get the implementation of the UserDataInterface.
     *
     * @return UserDataInterface|null
     * @throws InvalidConfigException
     */
    public function getUserData()
    {
        if (is_null($this->userData) && !is_null($this->userDataClass)) {
            $this->userData = Yii::createObject($this->userDataClass);
        }

        return $this->userData;
    }

    /**
     * @return BaseAccessControl
     */
    public function getAccessControl()
    {
        return $this->accessControl;
    }

    /**
     * Make config valid, set unspecified fields to default values.
     *
     * @throws InvalidConfigException
     */
    protected function normalizeModelsConfig()
    {
        $config = [];
        foreach ($this->modelsConfig as $modelConfig) {
            if (is_string($modelConfig)) {
                $modelConfig = ['class' => $modelConfig];
            } elseif (!isset($modelConfig['class'])) {
                throw new InvalidConfigException('Each modelConfig item must contain "class" field.');
            }
            $url = isset($modelConfig['url'])
                ? $modelConfig['url']
                : Inflector::camel2id(StringHelper::basename($modelConfig['class']));
            $labels = [];
            foreach ([true, false, false] as $pos => $plurals) {
                $labels[$pos] = isset($modelConfig['labels'][$pos])
                    ? $modelConfig['labels'][$pos]
                    : ($plurals
                        ? Inflector::pluralize(StringHelper::basename($modelConfig['class']))
                        : StringHelper::basename($modelConfig['class'])
                    );
            }
            $config[$url] = [
                'class' => $modelConfig['class'],
                'labels' => $labels,
                'menuIcon' => isset($modelConfig['menuIcon']) ? $modelConfig['menuIcon'] : 'dashboard',
                'url' => $url,
            ];
        }
        $this->modelsConfig = $config;
    }

    /**
     * Register needed i18n files
     */
    protected function registerTranslations()
    {
        if (!isset(\Yii::$app->i18n->translations['ylab/administer'])
            && !isset(\Yii::$app->i18n->translations['ylab/administer/*'])
        ) {
            \Yii::$app->i18n->translations['ylab/administer'] = [
                'class' => PhpMessageSource::class,
                'basePath' => '@ylab/administer/messages',
                'forceTranslation' => true,
                'fileMap' => [
                    'ylab/administer' => 'administer.php',
                ],
            ];
        }
    }

    /**
     * Register classes in DI.
     */
    protected function registerDI()
    {
        if (!Yii::$container->has(AutocompleteServiceInterface::class)) {
            Yii::$container->set(AutocompleteServiceInterface::class, AutocompleteService::class);
        }
    }

    /**
     * Configure url manager of application.
     * @throws InvalidConfigException
     */
    protected function configureUrlManager()
    {
        $urlManager = \Yii::$app->getUrlManager();
        $rules = [];

        if ($this->getUserData() !== null) {
            $rules["$this->urlPrefix/logout"] = "$this->id/user/logout";
            if ($this->getUserData()->getLoginForm() !== null) {
                $rules["$this->urlPrefix/login"] = "$this->id/user/login";
            }
        }

        $rules = ArrayHelper::merge($rules, [
            "$this->urlPrefix" => "$this->id/crud/default",
            "$this->urlPrefix/<modelClass:[\\w-]+>" => "$this->id/crud/index",
            "$this->urlPrefix/<modelClass:[\\w-]+>/<action:(autocomplete)>/<id:\\d+>" => "$this->id/api/<action>",
            "$this->urlPrefix/<modelClass:[\\w-]+>/<action:[\\w-]+>" => "$this->id/crud/<action>",
            "$this->urlPrefix/<modelClass:[\\w-]+>/<action:[\\w-]+>/<id:\\d+>" => "$this->id/crud/<action>",
        ]);

        $urlManager->addRules($rules);
    }

    /**
     * Configure of access control component.
     * @throws InvalidConfigException
     */
    protected function configureAccessControl()
    {
        $accessConfig = ArrayHelper::merge(
            ['class' => AccessControl::class],
            $this->access
        );
        $this->accessControl = Yii::createObject($accessConfig);
    }
}
