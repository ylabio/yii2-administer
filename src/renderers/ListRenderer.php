<?php

namespace ylab\administer\renderers;

use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\ArrayHelper;
use ylab\administer\SearchModelInterface;

/**
 * Class for GridView rendering
 */
class ListRenderer
{
    /**
     * @var null|SearchModelInterface
     */
    public $searchModel;
    /**
     * Array of attributes for GridView rendering.
     * Example:
     * ```
     * 'columns' => [
     *     'name',
     *     'avatar',
     *     'doc',
     * ],
     * // other GridView properties
     * 'overwriteColumns' => [
     *     'name' => [
     *         'attribute' => 'name',
     *         'value => function ($model) {
     *             return ucfirst($model->name);
     *         },
     *     ],
     *     'id' => false,
     *     'serialColumn' => false,
     *     'actionColumn => [
     *         'visibleButtons' => [
     *             'update' => false,
     *         ],
     *     ],
     * ],
     * ```
     *
     * @var array
     */
    public $gridWidgetConfig = [];
    /**
     * gridWidgetConfig field for serialColumn initialization.
     *
     * @var string
     */
    public $serialColumnField = 'serialColumn';
    /**
     * gridWidgetConfig field for actionColumn initialization.
     *
     * @var string
     */
    public $actionColumnField = 'actionColumn';

    /**
     * @var ConfigMerger
     */
    protected $configMerger;

    /**
     * DetailRenderer constructor.
     * @param ConfigMerger $configMerger
     */
    public function __construct(ConfigMerger $configMerger)
    {
        $this->configMerger = $configMerger;
    }

    /**
     * Render GridView widget and return is as a string.
     *
     * @param ActiveRecord $model
     * @param array $params
     * @param string $url
     * @return string
     * @throws \Exception
     */
    public function renderGrid(ActiveRecord $model, array $params, $url)
    {
        return GridView::widget($this->initGridWidgetConfig($model, $params, $url));
    }

    /**
     * Init default GridView config and merge it with user config.
     *
     * @param ActiveRecord $model
     * @param array $params
     * @param string $url
     * @return array
     */
    protected function initGridWidgetConfig(ActiveRecord $model, array $params, $url)
    {
        $config = [
            'layout' => "{items}\n<div class='row'>{summary}{pager}</div>",
            'columns' => [],
        ];

        if ($this->searchModel instanceof SearchModelInterface) {
            $config['dataProvider'] = new ActiveDataProvider(['query' => $this->searchModel->search($params)]);
            $config['filterModel'] = $this->searchModel;
        } else {
            $config['dataProvider'] = new ActiveDataProvider(['query' => $model::find()]);
        }

        if (isset($this->gridWidgetConfig['overwriteColumns'][$this->serialColumnField])) {
            $serialColumnConfig = $this->gridWidgetConfig['overwriteColumns'][$this->serialColumnField];
            if ($serialColumnConfig !== false) {
                $config['columns'][] = $serialColumnConfig;
            }
            unset($this->gridWidgetConfig['overwriteColumns'][$this->serialColumnField]);
        } else {
            $config['columns'][] = [
                'class' => SerialColumn::class,
            ];
        }

        $columns = [];
        if (isset($this->gridWidgetConfig['columns'])) {
            $columns = $this->gridWidgetConfig['columns'];
            unset($this->gridWidgetConfig['columns']);
        } else {
            foreach ($model as $name => $value) {
                if ($value === null || is_scalar($value) || is_callable([$value, '__toString'])) {
                    $columns[] = (string)$name;
                }
            }
        }
        $columns = $this->configMerger->merge(
            $columns,
            isset($this->gridWidgetConfig['overwriteColumns']) ? $this->gridWidgetConfig['overwriteColumns'] : []
        );
        $config['columns'] = ArrayHelper::merge($config['columns'], $columns);

        if (isset($this->gridWidgetConfig['overwriteColumns'][$this->actionColumnField])) {
            $actionColumnConfig = $this->gridWidgetConfig['overwriteColumns'][$this->actionColumnField];
            if ($actionColumnConfig !== false) {
                $config['columns'][] = $actionColumnConfig;
            }
            unset($this->gridWidgetConfig['overwriteColumns'][$this->actionColumnField]);
        } else {
            $config['columns'][] = [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, ActiveRecord $model) use ($url) {
                    return [
                        $action,
                        'modelClass' => $url,
                        'id' => $model->getPrimaryKey(),
                    ];
                },
            ];
        }

        if (isset($this->gridWidgetConfig['overwriteColumns'])) {
            unset($this->gridWidgetConfig['overwriteColumns']);
        }

        return ArrayHelper::merge($config, $this->gridWidgetConfig);
    }
}
