<?php

namespace ylab\administer\controllers;

use yii\data\ArrayDataProvider;
use yii\rest\Controller;
use ylab\administer\components\AutocompleteSerializer;
use ylab\administer\components\FindModelTrait;
use ylab\administer\components\ParamBindingTrait;
use ylab\administer\helpers\ModelHelper;
use ylab\administer\Module;

/**
 * Controller for API actions.
 *
 * @inheritdoc
 * @property Module $module
 */
class ApiController extends Controller
{
    use FindModelTrait, ParamBindingTrait;

    /**
     * @inheritdoc
     */
    public $serializer = AutocompleteSerializer::class;

    /**
     * Returns data for autocomplete in relational field.
     *
     * @param string $modelClass
     * @param string $id
     * @param string $relation Name of relation
     * @param string $key Attribute name for use as key
     * @param string $label Attribute name for display use
     * @param string $q Query text from user input
     * @return ArrayDataProvider
     */
    public function actionAutocomplete($modelClass, $relation, $key, $label, $q, $id = null)
    {
        if (!$id) {
            /** @var $model \yii\db\ActiveRecord */
            $model = new $modelClass();
            ModelHelper::ensureCrudViewBehavior($model);
        } else {
            $model = $this->findModel($modelClass, $id);
        }
        return new ArrayDataProvider([
            'allModels' => $model->getRelatedAutocompleteHintsData($relation, $key, $label, $q),
            'pagination' => false,
        ]);
    }
}