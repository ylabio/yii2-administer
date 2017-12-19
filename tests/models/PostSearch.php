<?php

namespace tests\models;

use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use ylab\administer\SearchModelInterface;

/**
 * @inheritdoc
 */
class PostSearch extends Post implements SearchModelInterface
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'author_id'], 'integer'],
            [['preview', 'text'], 'string'],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params)
    {
        $query = new ActiveQuery(static::class);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'author_id' => $this->author_id,
        ]);

        $query->andFilterWhere(['LIKE', 'preview', $this->preview])
            ->andFilterWhere(['LIKE', 'text', $this->text]);

        return $dataProvider;
    }
}
