# Menu configuration

To configure the menu, you need to add the menu configuration to the module's `menuConfig` property.  

The configuration is similar to the widget [yii\widgets\Menu](http://www.yiiframework.com/doc-2.0/yii-widgets-menu.html) 
and has an additional parameter `modelId` which specifies the model ID from the [configuration of the models](01-module-models-configuration.md).

The model ID is the value of `url` from the configuration of the model.

Example:
```php
'menuConfig' => [
     [
         'modelId' => 'post',
     ],
     [
         'label' => 'Directories',
         'icon' => 'list',
         'items' => [
             ['modelId' => 'post-tags'],
             [
                 'label' => 'Categories',
                 'url' => 'post-categories',
             ],
         ],
     ],
]
```
