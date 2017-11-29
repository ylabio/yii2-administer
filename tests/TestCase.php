<?php

namespace tests;

use tests\models\Post;
use tests\models\Tag;
use yii\db\Connection;
use yii\web\Application;
use yii\helpers\ArrayHelper;
use ylab\administer\Module;

/**
 * @inheritdoc
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->mockApplication();
        $this->addPostTable();
        $this->addAuthorTable();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        \Yii::$app = null;
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    protected function mockApplication(array $config = [], $appClass = Application::class)
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'test-app',
            'basePath' => __DIR__,
            'vendorPath' => dirname(__DIR__) . '/vendor',
            'aliases' => [
                '@bower' => '@vendor/bower-asset',
                '@npm' => '@vendor/npm-asset',
            ],
            'components' => [
                'request' => [
                    'cookieValidationKey' => 'NJndks8h23kndBH7f8bnf',
                    'scriptFile' => __DIR__ . '/index.php',
                    'scriptUrl' => '/index.php',
                    'enableCsrfValidation' => false,
                    'url' => '/test',
                ],
                'urlManager' => [
                    'enablePrettyUrl' => true,
                ],
                'db' => [
                    'class' => Connection::class,
                    'dsn' => 'sqlite::memory:',
                ],
                'assetManager' => [
                    'basePath' => '@tests/assets',
                    'baseUrl' => '/'
                ],
            ],
            'bootstrap' => ['admin'],
            'modules' => [
                'admin' => [
                    'class' => Module::class,
                    'urlPrefix' => 'backend',
                    'modelsConfig' => [
                        Post::class,
                        [
                            'class' => Tag::class,
                            'labels' => ['Теги', 'Тег', 'Тега'],
                            'menuIcon' => 'circle-o',
                            'url' => 'post-tags',
                        ],
                    ],
                ],
            ],
        ], $config));
    }

    /**
     * Create table for `Post` model.
     *
     * @throws \yii\db\Exception
     */
    protected function addPostTable()
    {
        $columns = [
            'id' => 'pk',
            'text' => 'string',
            'preview' => 'string',
            'author_id' => 'integer',
        ];
        \Yii::$app->getDb()->createCommand()->createTable('post', $columns)->execute();
    }

    /**
     * Create table for `Author` model.
     *
     * @throws \yii\db\Exception
     */
    protected function addAuthorTable()
    {
        $columns = [
            'id' => 'pk',
            'name' => 'string',
            'age' => 'integer',
            'status' => 'integer',
        ];
        \Yii::$app->getDb()->createCommand()->createTable('author', $columns)->execute();
    }

    /**
     * Invokes a inaccessible method.
     *
     * @param $object
     * @param $method
     * @param array $args
     * @param bool $revoke whether to make method inaccessible after execution
     * @return mixed
     */
    protected function invokeMethod($object, $method, $args = [], $revoke = true)
    {
        $reflection = new \ReflectionObject($object);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);
        $result = $method->invokeArgs($object, $args);
        if ($revoke) {
            $method->setAccessible(false);
        }
        return $result;
    }
}
