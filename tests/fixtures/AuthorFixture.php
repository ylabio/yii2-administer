<?php

namespace tests\fixtures;

use tests\models\Author;
use yii\test\ActiveFixture;

/**
 * @inheritdoc
 */
class AuthorFixture extends ActiveFixture
{
    /**
     * @inheritdoc
     */
    public $modelClass = Author::class;
}
