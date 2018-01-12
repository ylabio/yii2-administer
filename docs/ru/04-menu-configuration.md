# Конфигурация меню

Для настройки меню необходимо добавить конфигурацию меню в свойство `menuConfig` модуля.  

Структура конфигурации аналогична виджету [yii\widgets\Menu](http://www.yiiframework.com/doc-2.0/yii-widgets-menu.html) 
и имеет дополнительный параметр `modelId` в котором указывается ID модели из [конфигурации моделей](01-module-models-configuration.md).

В качестве ID модели используется значение `url` из конфигурации модели.

Пример:
```php
'menuConfig' => [
     [
         'modelId' => 'post',
     ],
     [
         'label' => 'Справочники',
         'icon' => 'list',
         'items' => [
             ['modelId' => 'post-tags'],
             [
                 'label' => 'Категории',
                 'url' => 'post-categories',
             ],
         ],
     ],
]
```
