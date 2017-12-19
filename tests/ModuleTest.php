<?php

namespace tests;

use tests\models\Post;
use tests\models\Tag;

/**
 * @inheritdoc
 */
class ModuleTest extends TestCase
{
    /**
     * Tests Module::init()
     */
    public function testInit()
    {
        $this->assertArrayHasKey('ylab/administer', \Yii::$app->i18n->translations);

        $rules = [
            'backend' => 'admin/crud/default',
            'backend/<modelClass:[\\w-]+>' => 'admin/crud/index',
            'backend/<modelClass:[\\w-]+>/<action:[\\w-]+>' => 'admin/crud/<action>',
            'backend/<modelClass:[\\w-]+>/<action:[\\w-]+>/<id:\\d+>' => 'admin/crud/<action>',
        ];
        $this->assertCount(count($rules), \Yii::$app->getUrlManager()->rules);
        foreach (\Yii::$app->getUrlManager()->rules as $rule) {
            if (array_key_exists($rule->name, $rules) && $rules[$rule->name] === $rule->route) {
                unset($rules[$rule->name]);
            }
        }
        $this->assertCount(0, $rules);

        $this->assertEquals(
            [
                'post' => [
                    'class' => Post::class,
                    'url' => 'post',
                    'labels' => ['Posts', 'Post', 'Post'],
                    'menuIcon' => 'dashboard',
                ],
                'post-tags' => [
                    'class' => Tag::class,
                    'labels' => ['Теги', 'Тег', 'Тега'],
                    'menuIcon' => 'circle-o',
                    'url' => 'post-tags',
                ],
            ],
            \Yii::$app->modules['admin']->modelsConfig
        );
    }

    /**
     * Tests Module::getMenuItems()
     */
    public function testGetMenuItems()
    {
        $this->assertEquals(
            [
                [
                    'label' => 'Posts',
                    'icon' => 'dashboard',
                    'url' => ['index', 'modelClass' => 'post'],
                ],
                [
                    'label' => 'Теги',
                    'icon' => 'circle-o',
                    'url' => ['index', 'modelClass' => 'post-tags'],
                ],
            ],
            \Yii::$app->modules['admin']->getMenuItems()
        );
    }
}
