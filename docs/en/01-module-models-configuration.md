# Module models configuration

For models configuring need add in `modelsConfig` module property config of each model. You can do this simply by adding
the model class to the array:
```php
'modelsConfig' => [
    \app\models\Post::class,
],
```
or add array of model config
```php
'modelsConfig' => [
    [
        'class' => \app\models\Post::class,
        'url' => 'user-posts',
        'menuIcon' => 'dashboard',
        'labels' => ['Posts', 'Post'],
    ],
],
```
where:
- `class` - name of model class
- `url` - URL part after module ID
- `menuIcon` - icon style in sidebar menu (see [FontAwesome icons](http://fontawesome.io/icons/))
- `labels` - array of model names as [plural name, singular name]
