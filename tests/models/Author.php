<?php

namespace tests\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "author".
 *
 * @property integer $id
 * @property string $name
 * @property integer $age
 * @property integer $status
 *
 * @property Comment[] $comments
 * @property Post[] $posts
 */
class Author extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'author';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::class, ['author_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::class, ['author_id' => 'id']);
    }
}
