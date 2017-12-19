<?php

namespace tests\fixtures;

use tests\models\Post;
use yii\test\ActiveFixture;

/**
 * @inheritdoc
 */
class PostFixture extends ActiveFixture
{
    /**
     * @inheritdoc
     */
    public $modelClass = Post::class;
    /**
     * @inheritdoc
     */
    public $depends = [AuthorFixture::class];
}
