<?php

namespace ylab\administer\renderers;

use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\grid\ActionColumn;
use ylab\administer\components\data\ActiveDataProvider;
use ylab\administer\components\data\FilterModelInterface;
use ylab\administer\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\ArrayHelper;
use yii\validators\DateValidator;
use ylab\administer\grid\filter\advanced\BaseFilterInput;
use ylab\administer\grid\filter\base\DateIntervalFilterInput;
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
     *     'published_at',
     * ],
     * // other GridView properties
     * 'overwriteColumns' => [
     *     'name' => [
     *         'attribute' => 'name',
     *         'value' => function ($model) {
     *             return ucfirst($model->name);
     *         },
     *     ],
     *     'id' => false,
     *     'published_at' => [
     *         'attribute' => 'published_at',
     *         'filterClass' => DateIntervalFilterInput::class,
     *     ],
     *     'serialColumn' => false,
     *     'actionColumn' => [
     *         'class' => ActionColumn::class,
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

        if ($this->searchModel instanceof FilterModelInterface) {
            $searchModel = $this->searchModel;
            $config['dataProvider'] = new ActiveDataProvider($this->searchModel->getDataProviderConfig());
            $config['filterModel'] = $this->searchModel;
        } elseif ($this->searchModel instanceof SearchModelInterface) {
            $config['dataProvider'] = $this->searchModel->search($params);
            $config['filterModel'] = $this->searchModel;
            $config['filterPosition'] = GridView::FILTER_POS_BODY;
        } else {
            $config['dataProvider'] = new ActiveDataProvider(['query' => $model::find()]);
            $config['filterPosition'] = GridView::FILTER_POS_BODY;
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
        $config['columns'] = $this->configureColumnFilters($model, $config['columns']);

        if (isset($this->gridWidgetConfig['overwriteColumns'][$this->actionColumnField])) {
            $actionColumnConfig = $this->gridWidgetConfig['overwriteColumns'][$this->actionColumnField];
            if ($actionColumnConfig !== false) {
                $config['columns'][] = $actionColumnConfig;
            }
            unset($this->gridWidgetConfig['overwriteColumns'][$this->actionColumnField]);
        } else {
            $config['columns'][] = [
                'class' => ActionColumn::class,
                'template' => '{delete}',
                'urlCreator' => function ($action, ActiveRecord $model) use ($url) {
                    return [
                        $action,
                        'modelClass' => $url,
                        'id' => $model->getPrimaryKey(),
                    ];
                },
            ];
        }
        $config['url'] = $url;

        if (isset($this->gridWidgetConfig['overwriteColumns'])) {
            unset($this->gridWidgetConfig['overwriteColumns']);
        }

        return ArrayHelper::merge($config, $this->gridWidgetConfig);
    }

    /**
     * Configuration of filters for the columns.
     *
     * @param ActiveRecord $model
     * @param array $columns
     * @return array List of columns with filter configurations
     */
    private function configureColumnFilters(ActiveRecord $model, array $columns)
    {
        $prepared = [];
        $defaultColumnFilters = $this->initDefaultFiltersForColumns($model);

        foreach ($columns as $key => $column) {
            // just string with attribute name
            if (!is_array($column)) {
                $column = ['attribute' => $column];
            }

            // missing required option 'attribute' in config
            if (!ArrayHelper::keyExists('attribute', $column)) {
                throw new InvalidParamException("Option 'attribute' is required.");
            }

            if (
                !ArrayHelper::keyExists('filter', $column)
                && !ArrayHelper::keyExists('filterClass', $column)
                && ArrayHelper::keyExists($column['attribute'], $defaultColumnFilters)
            ) {
                // adds default filter
                $column = ArrayHelper::merge($column, $defaultColumnFilters[$column['attribute']]);
            }

            $filterClass = ArrayHelper::remove($column, 'filterClass');

            // filter is disabled
            if (ArrayHelper::keyExists('filter', $column) && $column['filter'] === false) {
                $prepared[$key] = $column;
                continue;
            }

            // filter class is specified
            if ($filterClass) {
                if (!class_exists($filterClass) || !is_subclass_of($filterClass, BaseFilterInput::class)) {
                    throw new InvalidParamException("Filter class {$filterClass} must be instance of " . BaseFilterInput::class);
                }

                if ($this->searchModel instanceof ActiveRecord) {
                    /* @var BaseFilterInput $filter */
                    $filter = new $filterClass($this->searchModel, $column['attribute']);
                    $column['filter'] = $filter->render(ArrayHelper::getValue($column, 'filterInputOptions', []));
                }
            }

            $prepared[$key] = $column;
        }

        return $prepared;
    }

    /**
     * Create config with default filter for columns of model.
     *
     * @param ActiveRecord $model
     * @return array
     */
    private function initDefaultFiltersForColumns(ActiveRecord $model)
    {
        $config = [];

        foreach ($model->getActiveValidators() as $validator) {
            $class = get_class($validator);
            $filter = null;

            switch ($class) {
                case DateValidator::class:
                    $filter = DateIntervalFilterInput::class;
                    break;
                default:
                    continue;
            }

            $config = ArrayHelper::merge($config, $this->setFilterForAttributes($filter, $validator->getAttributeNames()));
        }

        return $config;
    }

    /**
     * Create filter config for scope of attributes.
     *
     * @param string|array|null|false $filter
     * @param array $attributes
     * @return array
     */
    private function setFilterForAttributes($filter, array $attributes)
    {
        $parameterName = 'filter';

        if (class_exists($filter)) { // for specified existing class
            $parameterName = 'filterClass';
        }

        // create config
        $config = [];

        foreach ($attributes as $attribute) {
            $config[$attribute] = [$parameterName => $filter];
        }

        return $config;
    }
}
