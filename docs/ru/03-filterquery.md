# Использование фильтрации с помощью `FilterQuery`
     
Фильтрация реализуется в модели для поиска которая реализует `SearchModelInterface`.    
Метод `FilterQuery::addInterval(string $attribute, mixed $value, [$separator])` позволяет добавить к запросу фильтрацию по интервалу.
 
Пример использования:

```php
class ArticleSearch extends Article implements SearchModelInterface
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['published_at', 'title'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function search(array $params)
    {
        $query = Article::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        
        $filterQuery = new FilterQuery($query);
        $filterQuery->addInterval('published_at', $this->published_at);

        $query->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
    
    // ...

}
```
