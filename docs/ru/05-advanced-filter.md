# Использование продвинутой фильтрации
     
Продвинутая фильтрация реализуется в модели, которая реализует `FilterModelInterface`.
 
Пример использования:

```php
class JournalPageNewsSearch extends JournalPageNews implements FilterModelInterface
{
    private $query;

    public function rules()
    {
        return [
            [['id', 'sort', 'status'], 'integer'],
            [['is_published', 'is_rss_published'], 'boolean'],
            [['title', 'subtitle', 'image', 'content', 'published_at', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    public function filters()
    {
        return [
            'title' => [
                'class' => OperatorFilterInput::class,
                'operators' => [
                    '~' => Yii::t('app', 'Содержит'),
                    '!~' => Yii::t('app', 'Не содержит'),
                ],
            ],
            'is_published' => [
                'class' => SelectFilterInput::class,
                'values' => [
                    null => Yii::t('app', 'Любой'),
                    1 => Yii::t('app', 'Да'),
                    0 => Yii::t('app', 'Нет'),
                ],
            ],
            'published_at' => [
                'class' => DateIntervalFilterInput::class,
            ],
            'tags_ids' => [
                'class' => MultiSelectFilterInput::class,
                'modelClass' => Tag::class,
                'relationAttribute' => 'tags',
                'operator' => 'in',
            ],
        ];
    }
    
    public function getDataProviderConfig()
    {
        return [
            'query' => JournalPageNews::find()->where(['type' => static::TYPE_NEWS]),
            'customFilterOperators' => [
                'in' => EqualTaxonomyFilterOperator::class,
            ]
        ];
    }
}
```
