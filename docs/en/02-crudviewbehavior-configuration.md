# CrudViewBehavior configuration

`CrudViewBehavior` has following properties:
- `formRenderer` - `FormRenderer` class configuration
- `listRenderer` - `ListRenderer` class configuration
- `detailRenderer` - `DetailRenderer` class configuration
- `buttonsConfig` - configuration of buttons

## FormRenderer

`FormRenderer` render model form on `create` and `update` pages.
`FormRenderer` contain property `attributesInputs`, as:
```php
'formRenderer' => [
    'attributesInputs' => [
        'id',// field shown in form, type determined based on `rules()` method
        'image' => [
            'class' => \ylab\administer\fields\ImageField::class,
        ],// fiels shown in form, type set as 'image'
        'phone' => [
            'class' => \ylab\administer\fields\WidgetField::class, 
            'options' => [
                'class' => \yii\widgets\MaskedInput::class, // class of widget
                'mask' => '999-99-99',
            ],
        ],
        // other fields
    ],
],
```
If field is not set in `attributesInputs`, then it is not displayed in form.


If `attributesInputs` property not set or is an empty array, then form contain all model attributes.

#### Attribute classes

Basic classes to display attributes:
- `\ylab\administer\fields\EmailField` - generated `<input type="email">`
- `\ylab\administer\fields\NumberField` - generated `<input type="number">`
- `\ylab\administer\fields\StringField` - generated `<input type="text">`
- `\ylab\administer\fields\FileField` - generated `<input type="file">`
- `\ylab\administer\fields\ImageField` - generated `<input type="file">` and `<div>` with image, which updating after `onchange()` events triggering.
- `\ylab\administer\fields\DropdownField` - generated `<select>...</select>`
- `\ylab\administer\fields\CheckboxField` - generated `<input type="checkbox">` внутри `<label>` с подписью для атрибута.
- `\ylab\administer\fields\TextareaField` - generated `<textarea>`
- `\ylab\administer\fields\WidgetField` - uses the specified widget, its class must be specified in the array `options`

Classes for the output of attributes extends `\ylab\administer\fields\BaseField`.   

## ListRenderer

`ListRenderer` render `GridView` widget on `index` page. The configuration is as follows:
```php
'listRenderer' => [
    'searchModel' => \app\models\search\PostSearch::class,
    'gridWidgetConfig' => [
        'columns' => [
            'id',
            'name',
            'image',
        ],
        'overwriteColumns' => [
            'name' => [
                'attribute' => 'name',
                'value' => function ($model) {
                    return ucfirst($model->name);
                },
            ],
            'id' => false,
            'serialColumn' => false,
            'actionColumn' => [
                'visibleButtons' => [
                    'update' => false,
                ],
            ],
        ],
    ],
    'serialColumnField' => 'serialColumn',
    'actionColumnField' => 'actionColumn',
],
```
, where
- `searchModel` -  model for searching, that implement `SearchModelInterface`. If not set, used `find()` method of
model.
- `gridWidgetConfig` - [GridView](http://www.yiiframework.com/doc-2.0/yii-grid-gridview.html) widget config with an
additional property:

    `overwriteColumns` - array of fields configurations, where as a key used model fields, as a value used field
    configuration. If as a value set `false`, then this fields not shown. Field configuration array is as
    [columns](http://www.yiiframework.com/doc-2.0/yii-grid-gridview.html#$columns-detail).

- `serialColumnField` - key in `overwriteColumns` for
[SerialColumn](http://www.yiiframework.com/doc-2.0/yii-grid-serialcolumn.html) configuration.

- `actionColumnField` - key in `overwriteColumns` for
[ActionColumn](http://www.yiiframework.com/doc-2.0/yii-grid-actioncolumn.html) configuration.

## DetailRenderer

`DetailRenderer` render `DetailView` widget on `view` page. The configuration is as follows:
```php
'detailRenderer' => [
    'detailWidgetConfig' => [
        'attributes' => [
            'id',
            'name',
            'image',
        ],
        'overwriteAttributes' => [
            'name' => [
                'attribute' => 'name',
                'value' => function ($model) {
                    return ucfirst($model->name);
                },
            ],
            'id' => false,
        ],
    ],
],
```
, where
- `detailWidgetConfig` - [DetailView](http://www.yiiframework.com/doc-2.0/yii-widgets-detailview.html) widget config
with additional property:

    `overwriteAttributes` - array of fields configurations, where as a key used model fields, as a value used field
    configuration. If as a value set `false`, then this fields not shown. Field configuration array is as
    [attributes](http://www.yiiframework.com/doc-2.0/yii-widgets-detailview.html#$attributes-detail).

## ButtonsConfig

`buttonsConfig` property is an array and configure additional page buttons.

Each item of array must be as following:
```php
'buttonsConfig' => [
    \ylab\administer\buttons\AbstractButton::TYPE_CREATE => [
        'text' => 'Add Post',
        'action' => 'create',
        'options' => [
            'class' => 'btn btn-danger',
        ],
    ],
],
```
where,
- `text` - button text
- `action` - action of controller for link
- `options` - additional button HTML-attributes

There are 5 types of buttons:
- `\ylab\administer\buttons\AbstractButton::TYPE_INDEX`
- `\ylab\administer\buttons\AbstractButton::TYPE_VIEW`
- `\ylab\administer\buttons\AbstractButton::TYPE_CREATE`
- `\ylab\administer\buttons\AbstractButton::TYPE_UPDATE`
- `\ylab\administer\buttons\AbstractButton::TYPE_DELETE`
