# Использование поведения CrudViewBehavior

Поведение `CrudViewBehavior` имеет следующие параметры:
- `formRenderer` - массив конфигурации класса `FormRenderer`
- `listRenderer` - массив конфигурации класса `ListRenderer`
- `detailRenderer` - массив конфигурации класса `DetailRenderer`
- `buttonsConfig` - массив конфигурации кнопок на CRUD страницах

## FormRenderer

`FormRenderer` является классом, ответственным за вывод формы на страницах создания и редактирования модели.
`FormRenderer` содержит свойство `attributesInputs`, конфигурация которого выглядит следующим образом:
```php
'formRenderer' => [
    'attributesInputs' => [
        'id',// поле выводится в форме, тип определяется на основе метода `rules()` модели
        'image' => [
            'class' => \ylab\administer\fields\ImageField::class,
        ],// поле выводится в форме, класс вывода поля установлен принудительно
        'phone' => [
            'class' => \ylab\administer\fields\WidgetField::class, 
            'options' => [
                'class' => \yii\widgets\MaskedInput::class, // класс виджета
                'mask' => '999-99-99',
            ],
        ],
        // остальные поля
    ],
],
```
Если поле не присутствует в `attributesInputs`, то в форме оно не выводится.

Если свойство `attributesInputs` не установлено или является пустым массивом, то в форме выводятся все атрибуты модели.

#### Классы атрибутов

Основные классы для вывода атрибутов:
- `\ylab\administer\fields\EmailField` - генерируется `<input type="email">`
- `\ylab\administer\fields\NumberField` - генерируется `<input type="number">`
- `\ylab\administer\fields\StringField` - генерируется `<input type="text">`
- `\ylab\administer\fields\FileField` - генерируется `<input type="file">`
- `\ylab\administer\fields\ImageField` - генерируется `<input type="file">`, под полем генерируется `<div>` с загруженным изображением, обновляется
после срабатывания события `onchange()` поля.
- `\ylab\administer\fields\DropdownField` - генерируется `<select>...</select>`
- `\ylab\administer\fields\CheckboxField` - генерируется `<input type="checkbox">` внутри `<label>` с подписью для атрибута.
- `\ylab\administer\fields\TextareaField` - генерируется `<textarea>`
- `\ylab\administer\fields\WidgetField` - использует указанный виджет, его класс необходимо указать в массиве `options`

Классы для вывода атрибутов расширяют `\ylab\administer\fields\BaseField`.   

## ListRenderer

`ListRenderer` является классом, ответственным за вывод виджета `GridView` на странице постраничного просмотра всех
моделей. Его конфигурация выглядит следующим образом:
```php
'listRenderer' => [
    'searchModel' => \app\models\search\PostSearch::class,
    'gridWidgetConfig' => [
        'columns' => [
            'id',
            'name',
            'image',
            'published_at',
        ],
        'overwriteColumns' => [
            'name' => [
                'attribute' => 'name',
                'value' => function ($model) {
                    return ucfirst($model->name);
                },
            ],
            'id' => false,
            'published_at' => [
                'attribute' => 'published_at',
                'filterClass' => DateIntervalFilterInput::class,
            ],
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
, где
- `searchModel` -  модель для поиска, которая имплементирует `SearchModelInterface`. Если не установлено, то в качестве
`ActiveQuery` используется метод `find()` модели.
- `gridWidgetConfig` - конфигурация виджета [GridView](http://stuff.cebe.cc/yii2docs-ru/yii-grid-gridview.html), в
которой используется дополнительное свойство:

    `overwriteColumns` - массив конфигураций полей, где в качестве ключей выступают поля модели, а в качестве значений -
    конфигурация поля. Если в качестве значения установлено `false`, то это поле не отображается. Массив конфигурации
    аналогичен полю [columns](http://stuff.cebe.cc/yii2docs-ru/yii-grid-gridview.html#$columns-detail).

- `serialColumnField` - название ключа в `overwriteColumns` для конфигурации
[SerialColumn](http://stuff.cebe.cc/yii2docs-ru/yii-grid-serialcolumn.html).

- `actionColumnField` - название ключа в `overwriteColumns` для конфигурации
[ActionColumn](http://stuff.cebe.cc/yii2docs-ru/yii-grid-actioncolumn.html).

## DetailRenderer

`DetailRenderer` является классом, ответственным за вывод виджета `DetailView` на странице просмотра модели. Его
конфигурация выглядит следующим образом:
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
, где
- `detailWidgetConfig` - конфигурация виджета
[DetailView](http://stuff.cebe.cc/yii2docs-ru/yii-widgets-detailview.html), в которой используется дополнительное
свойство:

    `overwriteAttributes` - массив конфигураций полей, где в качестве ключей выступают поля модели, а в качестве
    значений - конфигурация поля. Если в качестве значения установлено `false`, то это поле не отображается. Массив
    конфигурации аналогичен полю
    [attributes](http://stuff.cebe.cc/yii2docs-ru/yii-widgets-detailview.html#$attributes-detail).

## ButtonsConfig

Свойство `buttonsConfig` является массивом и отвечает за отображение кнопок дополнительных действий на CRUD страницах.

Каждый элемент массива должен выглядеть следующим образом:
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
где,
- `text` - текст кнопки
- `action` - экшн контроллера для ссылки
- `options` - дополнительные HTML-атрибуты кнопки

Всего есть 5 типов кнопок:
- `\ylab\administer\buttons\AbstractButton::TYPE_INDEX`
- `\ylab\administer\buttons\AbstractButton::TYPE_VIEW`
- `\ylab\administer\buttons\AbstractButton::TYPE_CREATE`
- `\ylab\administer\buttons\AbstractButton::TYPE_UPDATE`
- `\ylab\administer\buttons\AbstractButton::TYPE_DELETE`
