# Using filtering with `FilterQuery`
     
Filtering is implemented in the search model that implements `SearchModelInterface`.    
Method `FilterQuery::addInterval(string $attribute, mixed $value, [$separator])` allows you to add to the query interval filtering.
 
Example of use:

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
