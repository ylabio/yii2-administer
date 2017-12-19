<?php

namespace tests;

use tests\models\Post;
use yii\base\InvalidConfigException;
use yii\widgets\ActiveForm;
use ylab\administer\FormField;

/**
 * @inheritdoc
 */
class FormFieldTest extends TestCase
{
    /**
     * Tests FormField::createField()
     */
    public function testCreateField()
    {
        ob_start();
        ob_implicit_flush(false);
        $model = new Post();
        $form = ActiveForm::begin(['action' => '/backend/post']);

        $email = FormField::createField($form->field($model, 'text'), FormField::TYPE_EMAIL);
        $this->assertRegExp('/<input type="email"/', $email);

        $model->text = 'text';
        $image = FormField::createField($form->field($model, 'text'), FormField::TYPE_IMAGE);
        $this->assertRegExp('/<input type="file"/', $image);
        $this->assertRegExp('/<img src="text"/', $image);

        $file = FormField::createField($form->field($model, 'text'), FormField::TYPE_FILE, ['class' => 'test']);
        $this->assertRegExp('/<input type="file"/', $file);
        $this->assertRegExp('/class="test"/', $file);

        $number = FormField::createField($form->field($model, 'text'), FormField::TYPE_NUMBER);
        $this->assertRegExp('/<input type="number"/', $number);

        $string = FormField::createField($form->field($model, 'text'), FormField::TYPE_STRING, ['data-test' => 'test']);
        $this->assertRegExp('/<input type="text" (.)* data-test="test">/', $string);

        ActiveForm::end();
        ob_get_clean();

        $this->expectException(InvalidConfigException::class);
        FormField::createField($form->field($model, 'text'), 'test');
    }
}
