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
                'operators' => [
                    '~' => Yii::t('app', 'Содержит'),
                    '!~' => Yii::t('app', 'Не содержит'),
                ],
            ],
            'is_published' => [
                'values' => [
                    null => Yii::t('app', 'Любой'),
                    1 => Yii::t('app', 'Да'),
                    0 => Yii::t('app', 'Нет'),
                ],
            ],
        ];
    }
    
    public function getQuery()
    {
        if (is_null($this->query)) {
            $this->query = JournalPageNews::find()->where(['type' => static::TYPE_NEWS]);
        }
        
        return $this->query;
    }
}
```
