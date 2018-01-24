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
3) Configure filtering in the model for search (there are two ways,
see [Using filtering with FilterQuery](docs/en/03-filterquery.md)
or [Advanced filtering settings](docs/en/05-advanced-filter.md)
4) Configure menu (see [Menu configuring](docs/ru/04-menu-configuration.md))
5) Open URL `http://app_url/module_id`
6) Module is ready

## Access control

To configure access, you must define the `access` property in the module configuration, which must have the following structure:

- defaultRole: string, the role name used to configure the access control filter `\yii\filters\AccessControl`. Applies to crud and api controllers.
- rules: array, each element must contain the url model as the key and an array of roles as the value. 
To check the roles, the call to `\yii\web\User::can()` will be used.

Example of configuration:
```php
'access' => [
    'defaultRole' => 'admin',
    'rules' => [
        'post' => ['contentManager', 'admin'],
        'post-tags' => [],
    ],
],
```

## Additional features
In addition, it is proposed to connect the authorization and exit capability of the user.

To connect the output of the exit button from the panel, you need to implement the interface `UserDataInterface`:
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
To connect the authorization form, the `getLoginForm ()` method must
return implementation of the interface `LoginFormInterface`. In this case, it must necessarily be
the `yii\base\Model` object. Example:
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

## Testing

For testing run following command:
```bash
php vendor/bin/phpunit
```
