<?php

namespace ylab\administer\renderers;

use yii\db\ActiveRecord;
use yii\widgets\DetailView;

/**
 * Class for DetailView rendering
 */
class DetailRenderer
{
    /**
     * Array of attributes for DetailView rendering.
     * Example:
     * ```
     * 'attributes' => [
     *     'name',
     *     'avatar',
     *     'doc',
     * ],
     * // other DetailView properties
     * 'overwriteAttributes' => [
     *     'name' => [
     *         'attribute' => 'name',
     *         'value => function ($model) {
     *             return ucfirst($model->name);
     *         },
     *     ],
     *     'id' => false,
     * ],
     * ```
     *
     * @var array
     */
    public $detailWidgetConfig = [];

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
     * Render DetailView widget and return it as a string.
     *
     * @param ActiveRecord $model
     * @return string
     * @throws \Exception
     */
    public function renderDetailView(ActiveRecord $model)
    {
        if (isset($this->detailWidgetConfig['attributes'])) {
            $attributes = $this->detailWidgetConfig['attributes'];
        } else {
            $attributes = $model->attributes();
        }

        return DetailView::widget([
            'model' => $model,
            'attributes' => $this->configMerger->merge(
                $attributes,
                isset($this->detailWidgetConfig['overwriteAttributes'])
                    ? $this->detailWidgetConfig['overwriteAttributes']
                    : []
            )
        ]);
    }
}
