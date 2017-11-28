<?php

namespace tests;

use tests\fixtures\AuthorFixture;
use tests\fixtures\PostFixture;
use tests\models\Post;
use yii\test\FixtureTrait;

/**
 * @inheritdoc
 */
class CrudViewBehaviorTest extends TestCase
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
     * Tests CrudViewBehavior::renderForm()
     */
    public function testRenderForm()
    {
        $form = (new Post())->renderForm();
        $this->assertRegExp('/<input type="text"(.)*name="Post\[text\]"/', $form);
        $this->assertRegExp('/<input type="text"(.)*name="Post\[preview\]"/', $form);
        $this->assertRegExp('/<input type="number"(.)*name="Post\[author_id\]"/', $form);
    }

    /**
     * Tests CrudViewBehavior::renderGrid()
     */
    public function testRenderGrid()
    {
        $grid = (new Post())->renderGrid([], 'post');
        $this->assertRegExp('/data-key="1"/', $grid);
        $this->assertRegExp('/data-key="2"/', $grid);
        $this->assertRegExp('/<th>(.)*Id(.)*Text(.)*action-column(.)*<\/th>/', $grid);
        $this->assertNotRegExp('/<th>#<\/th>/', $grid);
    }

    /**
     * Tests CrudViewBehavior::renderDetailView()
     */
    public function testRenderDetailView()
    {
        $detailView = Post::findOne(1)->renderDetailView();
        $this->assertRegExp('/Id/', $detailView);
        $this->assertRegExp('/Text/', $detailView);
        $this->assertRegExp('/Preview/', $detailView);
        $this->assertRegExp('/Author Id(.)*<a href=(.)*\/test(.)*Ivan/', $detailView);
    }

    /**
     * Tests CrudViewBehavior::getButtons()
     */
    public function testGetButtons()
    {
        $model = new Post();

        $indexButtons = $model->getButtons('index', get_class($model));
        $this->assertCount(1, $indexButtons);
        $this->assertEquals('Create', $indexButtons[0]->text);

        $createButtons = $model->getButtons('create', get_class($model));
        $this->assertCount(1, $createButtons);
        $this->assertEquals('Button', $createButtons[0]->text);

        $model = Post::findOne(1);

        $viewButtons = $model->getButtons('view', get_class($model), $model->id);
        $this->assertCount(4, $viewButtons);

        $updateButtons = $model->getButtons('update', get_class($model), $model->id);
        $this->assertCount(4, $updateButtons);
    }

    /**
     * Tests CrudViewBehavior::getBreadcrumbs
     */
    public function testGetBreadcrumbs()
    {
        $model = new Post();

        $indexBreadcrumbs = $model->getBreadcrumbs('index', null, 'Posts');
        $this->assertCount(1, $indexBreadcrumbs);

        $createBreadcrumbs = $model->getBreadcrumbs('create', 'post', 'Posts');
        $this->assertCount(2, $createBreadcrumbs);

        $model = Post::findOne(1);

        $viewBreadcrumbs = $model->getBreadcrumbs('view', 'post', 'Posts', $model->id);
        $this->assertCount(2, $viewBreadcrumbs);

        $updateBreadcrumbs = $model->getBreadcrumbs('update', 'post', 'Posts', $model->id);
        $this->assertCount(3, $updateBreadcrumbs);
    }
}
