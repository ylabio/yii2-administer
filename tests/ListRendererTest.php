<?php

namespace tests;

use tests\fixtures\AuthorFixture;
use tests\fixtures\PostFixture;
use tests\models\Post;
use tests\models\PostSearch;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\test\FixtureTrait;
use ylab\administer\renderers\ConfigMerger;
use ylab\administer\renderers\ListRenderer;

/**
 * @inheritdoc
 */
class ListRendererTest extends TestCase
{
    use FixtureTrait;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->loadFixtures([new AuthorFixture(), new PostFixture()]);
        \Yii::$app->controller = \Yii::$app->createController('admin/crud/post')[0];
    }

    /**
     * Tests ListRenderer::renderGrid()
     */
    public function testRenderGrid()
    {
        $renderer = new ListRenderer(new ConfigMerger());
        $model = new Post;
        $grid = $renderer->renderGrid($model, [], 'post');
        $this->assertRegExp('/data-key="1"/', $grid);
        $this->assertRegExp('/data-key="2"/', $grid);
        $this->assertRegExp('/<th>(.)*Id(.)*Text(.)*Preview(.)*Author Id(.)*<\/th>/', $grid);

        $renderer->searchModel = new PostSearch();
        $renderer->actionColumnField = 'buttons';
        $renderer->gridWidgetConfig = [
            'columns' => [
                'id',
                'text',
                'preview',
                'author_id',
            ],
            'overwriteColumns' => [
                'author_id' => [
                    'attribute' => 'author_id',
                    'value' => function ($model) {
                        return ucfirst($model->author->name);
                    },
                ],
                'preview' => false,
                'serialColumn' => false,
                'buttons' => [
                    'class' => ActionColumn::class,
                    'visibleButtons' => [
                        'delete' => false,
                    ],
                ],
            ],
            'options' => ['class' => 'test-class'],
        ];
        $grid = $renderer->renderGrid($model, ['PostSearch' => ['id' => 1]], 'post');
        $this->assertRegExp('/data-key="1"/', $grid);
        $this->assertNotRegExp('/data-key="2"/', $grid);
        $this->assertRegExp('/<th>(.)*Id(.)*Text(.)*Author Id(.)*<\/th>/', $grid);
        $this->assertNotRegExp('/Preview/', $grid);
        $this->assertRegExp('/Ivan/', $grid);
        $this->assertNotRegExp('/Delete/', $grid);
        $this->assertRegExp('/<div(.)*class="test-class"/', $grid);
        $this->assertNotRegExp('/#/', $grid);

        $renderer->gridWidgetConfig['overwriteColumns']['serialColumn'] = [
            'class' => SerialColumn::class,
            'header' => '№',
        ];
        $grid = $renderer->renderGrid($model, [], 'post');
        $this->assertRegExp('/№/', $grid);
    }
}
