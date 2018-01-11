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
3) Настроить фильтрацию в модели для поиска (см. [Настройка фильтрации с помощью FilterQuery](docs/ru/03-filterquery.md))
4) Открыть URL-адрес `http://app_url/module_id`
5) Модуль готов к работе

## Дополнительные возможности
Дополнительно предлагается подключить возможность авторизации и выхода пользователя.

Чтобы подключить вывод кнопки выхода из панели, нужно реализовать интерфейс `UserDataInterface`:
```php
<?php

namespace common\components;

use common\models\LoginForm;
use Yii;
use ylab\administer\UserDataInterface;

class UserData implements UserDataInterface
{
    private $loginForm;
    
    public function getUserName()
    {
        return 'Ivan Petrov';
    }
    
    public function getAvatar()
    {
        return 'web/path/to/avatar';
    }
    
    public function getLoginForm()
    {
        if (is_null($this->loginForm)) {
            $this->loginForm = Yii::createObject(LoginForm::class);
        }

        return $this->loginForm;
    }
}

```
Чтобы подключить форму авторизации, метод `getLoginForm()` должен
возвращать реализацию интерфейса `LoginFormInterface`. При этом он обязательно должен быть
объектом `yii\base\Model`. Пример:
```php
<?php
namespace common\models;

use Yii;
use yii\base\Model;
use ylab\administer\LoginFormInterface;

class LoginForm extends Model implements LoginFormInterface
{
    public $email;
    public $password;
    public $rememberMe = true;

    public function rules()
    {
        // return rules
    }
    
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        
        return false;
    }
    
    protected function getUser()
    {
        if ($this->user === null) {
            $this->user = User::find()->byEmail($this->email)->one();
        }

        return $this->user;
    }
    
    public function getLoginAttribute()
    {
        return 'email';
    }
    
    public function getPasswordAttribute()
    {
        return 'password';
    }
    
    public function getRememberMeAttribute()
    {
        return 'rememberMe';
    }
}

```

## Тестирование

Для запуска тестов выполните следующую команду:
```bash
php vendor/bin/phpunit
```
