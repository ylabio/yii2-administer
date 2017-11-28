<?php

namespace ylab\administer;

use yii\data\ActiveDataProvider;

/**
 * Interface for ListRenderer::$searchModel
 */
interface SearchModelInterface
{
    /**
     * Creates data provider instance with search query applied.
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params);
}
