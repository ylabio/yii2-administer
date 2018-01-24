<?php

namespace tests\models;

use yii\db\ActiveRecord;
use yii\helpers\Html;
use ylab\administer\buttons\CrudButton;
use ylab\administer\CrudViewBehavior;
use ylab\administer\FormField;
use ylab\administer\renderers\FormRenderer;

/**
 * This is the model class for table "post".
 *
 * @property integer $id
 * @property string $text
 * @property string $preview
 * @property integer $author_id
 *
 * @property Comment[] $comments
 * @property Author $author
 * @property Tag[] $tags
 *
 * @mixin CrudViewBehavior
 */
class Post extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text'], 'string'],
            [['author_id'], 'integer'],
            [['preview'], 'string', 'max' => 255],
        ];
    }

    public function behaviors()
    {
        return [
            'crudView' => [
                'class' => CrudViewBehavior::class,
                'formRenderer' => [
                    'class' => FormRenderer::class,
                    'attributesInputs' => [
                        'text',
                        'preview',
                        'author_id' => [
                            'type' => FormField::TYPE_NUMBER,
                        ],
                    ],
                ],
                'buttonsConfig' => [
                    CrudButton::TYPE_INDEX => [
                        'text' => 'Button',
                        'options' => ['class' => 'btn btn-danger'],
                    ],
                ],
                'listRenderer' => [
                    'gridWidgetConfig' => [
                        'columns' => [
                            'id',
                            'text',
                        ],
                        'overwriteColumns' => [
                            'text' => [
                                'attribute' => 'text',
                                'value' => function ($model) {
                                    return strtoupper($model->text);
                                },
                            ],
                            'serialColumn' => false,
                        ],
                    ],
                ],
                'detailRenderer' => [
                    'detailWidgetConfig' => [
                        'attributes' => [
                            'id',
                            'text',
                            'preview',
                            'author_id',
                        ],
                        'overwriteAttributes' => [
                            'author_id' => [
                                'attribute' => 'author_id',
                                'value' => function ($model) {
                                    return Html::a($model->author->name, ['/test']);
                                },
                                'format' => 'raw',
                            ],
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::class, ['post_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])->viaTable('post_tag', ['post_id' => 'id']);
    }
}
