<?php

namespace ylab\administer\widgets;

use yii\grid\GridView as BaseGridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use ylab\administer\components\data\BaseFilter;
use ylab\administer\components\data\FilterModelInterface;
use Yii;

/**
 * Overrided base widget GridView with custom JS.
 */
class GridView extends BaseGridView
{
    const FILTER_POS_RIGHT = 'right';

    /**
     * @var string Url for creating link.
     */
    public $url = '';

    /**
     * @inheritdoc
     */
    public $filterPosition = self::FILTER_POS_RIGHT;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerJs();
        parent::run();
    }

    /**
     * @inheritdoc
     */
    public function renderItems()
    {
        if ($this->filterPosition === self::FILTER_POS_RIGHT) {
            $this->registerFilterJs();
            $this->registerFilterCss();
            return $this->renderFilters() . parent::renderItems();
        }

        return parent::renderItems();
    }

    /**
     * @inheritdoc
     */
    public function renderFilters()
    {
        if ($this->filterModel instanceof FilterModelInterface) {
            $cells = [];
            foreach ($this->filterModel->filters() as $attribute => $params) {
                $cells[] = $this->renderFilterCell($attribute, $params);
            }
            $filterButton = Html::button(Yii::t('ylab/administer', 'Filters'), [
                'class' => 'btn btn-success btn-flat filter-toggle',
            ]);

            return $filterButton . Html::tag(
                'div',
                Html::tag('h3', Yii::t('ylab/administer', 'Filters'), ['class' => 'filters-header'])
                    . Html::beginForm(['/admin/crud/index', 'modelClass' => $this->url], 'get')
                    . implode('', $cells)
                    . Html::tag('div', '', ['class' => 'filter-form-buttons-split'])
                    . Html::submitButton(Yii::t('ylab/administer', 'Filter'))
                    . Html::button(Yii::t('ylab/administer', 'Clear'), ['class' => 'clear-filters'])
                    . Html::endForm(),
                ['class' => 'filter-form']
            );
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    protected function renderFilterCell($attribute, array $params = [])
    {
        if (empty($params)) {
            $params[] = BaseFilter::DEFAULT_FILTER_OPERATOR;
        }
        $fieldName = 'filter[' . $attribute . ']';
        $fieldNameOperator = 'operator[' . $attribute . ']';
        $label = Html::tag('label', $this->filterModel->getAttributeLabel($attribute), [
            'class' => 'control-label',
        ]);
        if (array_key_exists('operators', $params) && is_array($params['operators'])) {
            return $label . Html::tag(
                'div',
                Html::dropDownList(
                        $fieldNameOperator,
                        ArrayHelper::getValue(\Yii::$app->request->getQueryParam('operator'), $attribute),
                        $params['operators'],
                        ['class' => 'form-control']
                    )
                    . Html::textInput(
                        $fieldName,
                        ArrayHelper::getValue(\Yii::$app->request->getQueryParam('filter'), $attribute),
                        ['class' => 'form-control']
                    ),
                ['class' => 'form-group']
            );
        } elseif (array_key_exists('values', $params) && is_array($params['values'])) {
            return $label . Html::tag(
                'div',
                Html::dropDownList(
                    $fieldName,
                    ArrayHelper::getValue(\Yii::$app->request->getQueryParam('filter'), $attribute),
                    $params['values'],
                    ['class' => 'form-control']
                ),
                ['class' => 'form-group']
            );
        }

        return '';
    }

    /**
     * Register JS scripts.
     */
    protected function registerJs()
    {
        $url = Url::to([
            'crud/update',
            'modelClass' => $this->url,
        ]);
        $js = <<<JS
$('.grid-view tbody').on('mouseenter', 'tr', function (e) {
    $(this).find('td').css({"background-color": "#f0f0f0", "cursor": "pointer", "border": "1px solid #f0f0f0"});
});
$('.grid-view tbody').on('mouseleave', 'tr', function (e) {
    $(this).find('td').css({"background-color": "#ffffff", "cursor": "arrow", "border": "1px solid #f4f4f4"});
});
$('.grid-view tbody').on('click', 'tr', function (e) {
    window.location.href = '{$url}/' + $(this).data('key');
});
JS;
        $this->getView()->registerJs($js);
    }

    /**
     * Register JS scripts for filter.
     */
    protected function registerFilterJs()
    {
        $js = <<<JS
$('.clear-filters').on('click', function (e) {
    $(this).parents('form')[0].reset();
});
$('.filter-toggle').on('click', function () {
    $('.filter-form').toggle();
});
JS;
        $this->getView()->registerJs($js);
    }

    /**
     * Register CSS for filter.
     */
    protected function registerFilterCss()
    {
        $css = <<<CSS
.filter-form {
    padding: 20px;
    position: absolute;
    right: 0;
    top: 70px;
    background: #ffffff;
    border: 1px solid #cccccc;
    z-index: 10;
    display: none;
}
.form-control {
    width: 150px;
    float: left;
}
.filter-form-buttons-split {
    padding: 8px;
    clear: left;
}
.filters-header {
    margin-top: 0;
}
.filter-toggle {
    margin: 20px;
    float: right;
}
CSS;
        $this->getView()->registerCss($css);
    }
}
