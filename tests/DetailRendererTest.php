<?php

namespace tests;

use tests\fixtures\AuthorFixture;
use tests\fixtures\PostFixture;
use tests\models\Post;
use yii\test\FixtureTrait;
use ylab\administer\renderers\ConfigMerger;
use ylab\administer\renderers\DetailRenderer;

/**
 * @inheritdoc
 */
class DetailRendererTest extends TestCase
{
    use FixtureTrait;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->loadFixtures([new AuthorFixture(), new PostFixture()]);
    }

    /**
     * Tests DetailRenderer::renderDetailView()
     */
    public function testRenderDetailView()
    {
        $renderer = new DetailRenderer(new ConfigMerger());
        $model = Post::findOne(1);
        $detailView = $renderer->renderDetailView($model);
        $this->assertRegExp('/Id/', $detailView);
        $this->assertRegExp('/Text/', $detailView);
        $this->assertRegExp('/Preview/', $detailView);
        $this->assertRegExp('/Author Id/', $detailView);

        $renderer->detailWidgetConfig = [
            'attributes' => [
                'id',
                'text',
                'preview',
                'author_id',
            ],
            'overwriteAttributes' => [
                'preview' => false,
                'author_id' => [
                    'attribute' => 'author_id',
                    'value' => function ($model) {
                        return ucfirst($model->author->name);
                    },
                ],
            ],
            'options' => [
                'class' => 'test-class',
            ],
        ];
        $detailView = $renderer->renderDetailView($model);
        $this->assertRegExp('/Id/', $detailView);
        $this->assertRegExp('/Text/', $detailView);
        $this->assertRegExp('/Author Id(.)*Ivan/', $detailView);
        $this->assertRegExp('/class="test-class"/', $detailView);
        $this->assertNotRegExp('/Preview/', $detailView);
    }
}
