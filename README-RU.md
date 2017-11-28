# Administer - админ-модуль для Yii2.

## Установка

Расширение устанавливается при помощи [composer](http://getcomposer.org/download).

Запустите в консоле команду
```bash
php composer.phar require ylab/yii2-administer "*"
```
или добавьте
```json
"ylab/yii2-administer": "*"
```
в `require` секцию файла `composer.json`.

Далее необходимо прописать модуль в конфигурации вашего приложения:
```php
'modules' => [
    ///
    'admin' => [
        'class' =>  \ylab\administer\Module::class,
        'urlPrefix' => 'admin',
    ],
]
```
и добавить модуль в `bootstrap` секцию конфигурации (необходимо для определения правил `UrlManager`)
```php
'bootstrap' => ['admin'],
```

## Использование

Для добавления и последующего использования модели в модуле необходимо проделать следующие шаги:

1) Сконфигурировать список моделей модуля (см. [Конфигурация моделей модуля](docs/ru/01-module-models-configuration.md))
2) К каждой модели из конфигурации присоединить поведение `CrudViewBehavior` (см.
[Использование поведения CrudViewBehavior](docs/ru/02-crudviewbehavior-configuration.md))
3) Открыть URL-адрес `http://app_url/module_id`
4) Модуль готов к работе
