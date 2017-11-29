<?php

namespace tests;

use tests\models\Post;
use ylab\administer\FormField;
use ylab\administer\renderers\FormRenderer;

/**
 * @inheritdoc
 */
class FormRendererTest extends TestCase
{
    /**
     * Tests FormRenderer::renderForm()
     */
    public function testRenderForm()
    {
        $renderer = new FormRenderer();
        $model = new Post();
        $renderer->attributesInputs = [
            'text',
            'preview',
            'author_id' => [
                'type' => FormField::TYPE_NUMBER,
            ],
        ];
        $defaultConfig = $this->invokeMethod($model->getBehavior('crudView'), 'getFieldsConfig');
        $form = $renderer->renderForm($model, $defaultConfig);
        $this->assertRegExp('/<input type="text"(.)*name="Post\[text\]"/', $form);
        $this->assertRegExp('/<input type="text"(.)*name="Post\[preview\]"/', $form);
        $this->assertRegExp('/<input type="number"(.)*name="Post\[author_id\]"/', $form);
        $this->assertRegExp('/<button type="submit"(.)*Create/', $form);
        $this->assertNotRegExp('/Post\[id\]/', $form);

        $model->setIsNewRecord(false);
        $model->addError('text', 'test-error');
        $form = $renderer->renderForm($model, $defaultConfig);
        $this->assertRegExp('/<div(.)*error-summary(.)*test-error/', $form);
        $this->assertRegExp('/<button type="submit"(.)*Save/', $form);

        $renderer->attributesInputs = [];
        $model = new Post();
        $form = $renderer->renderForm($model, $defaultConfig);
        $this->assertRegExp('/<input type="text"(.)*name="Post\[text\]"/', $form);
        $this->assertRegExp('/<input type="text"(.)*name="Post\[preview\]"/', $form);
        $this->assertRegExp('/<input type="number"(.)*name="Post\[author_id\]"/', $form);
        $this->assertRegExp('/<input type="text"(.)*name="Post\[id\]"/', $form);
    }
}
