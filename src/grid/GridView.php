<?php

namespace ylab\administer\grid;

use yii\grid\GridView as BaseGridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use ylab\administer\components\data\FilterModelInterface;
use Yii;
use ylab\administer\grid\filter\advanced\AdvancedFilterAsset;
use ylab\administer\grid\filter\advanced\BaseFilterInput;

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
            AdvancedFilterAsset::register($this->getView());
            $cells = [];
            foreach ($this->filterModel->filters() as $attribute => $params) {
                $cells[] = $this->renderFilterCell($attribute, $params);
            }

            return Html::tag(
                'div',
                Html::tag('h3', Yii::t('ylab/administer', 'Filters'), ['class' => 'filters-header'])
                    . Html::beginForm(['/admin/crud/index', 'modelClass' => $this->url], 'get')
                    . implode('', $cells)

                    . Html::submitButton(Yii::t('ylab/administer', 'Filter'))
                    . Html::button(Yii::t('ylab/administer', 'Clear'), ['class' => 'clear-filters'])
                    . Html::endForm(),
                ['class' => 'filter-form']
            );
        }

        return parent::renderFilters();
    }

    /**
     * @inheritdoc
     */
    protected function renderFilterCell($attribute, array $params = [])
    {
        $label = Html::tag('label', $this->filterModel->getAttributeLabel($attribute), [
            'class' => 'control-label',
        ]);
        /** @var BaseFilterInput $filterInput */
        $filterInput = Yii::createObject(ArrayHelper::merge($params, [
            'attribute' => $attribute,
            'modelUrl' => $this->url,
        ]));

        return $label
            . Html::tag('div', $filterInput->render(), ['class' => 'form-group'])
            . Html::tag('div', '', ['class' => 'filter-form-buttons-split']);
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
}
