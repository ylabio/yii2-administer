# Administer - admin module for Yii2.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download).

Either run
```bash
php composer.phar require ylab/yii2-administer "*"
```
or add
```json
"ylab/yii2-administer": "*"
```
in the `require` section of `composer.json` file.

Next, need add module initialization in app configuration:
```php
'modules' => [
    ///
    'admin' => [
        'class' =>  \ylab\administer\Module::class,
        'urlPrefix' => 'admin',
    ],
]
```
and add in `bootstrap` section (necessary for setting rules of `UrlManager`)
```php
'bootstrap' => ['admin'],
```

## Usage

For adding model in module, necessary take the following steps:

1) Configure list of models of module (see [Module models configuration](docs/en/01-module-models-configuration.md))
2) For every model attach `CrudViewBehavior` (see
[CrudViewBehavior configuration](docs/en/02-crudviewbehavior-configuration.md))
3) Open URL `http://app_url/module_id`
4) Module is ready

## Testing

For testing run following command:
```bash
php vendor/bin/phpunit
```
