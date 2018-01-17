<?php

namespace ylab\administer\widgets;

use yii\grid\GridView as BaseGridView;
use yii\helpers\Url;

/**
 * Overrided base widget GridView with custom JS.
 */
class GridView extends BaseGridView
{
    /**
     * @var string Url for creating link.
     */
    public $url = '';

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerJs();
        parent::run();
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
