<?php

namespace ylab\administer\controllers;

use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use ylab\administer\CrudViewBehavior;
use ylab\administer\Module;
use ylab\administer\ViewHelper;

/**
 * Controller for all CRUD actions.
 *
 * {@inheritdoc}
 * @property Module $module
 */
class CrudController extends Controller
{
    /**
     * @inheritdoc
     */
    public $layout = '@ylab/administer/views/layout';
    /**
     * Model class config.
     * Example:
     * ```
     * [
     *     'class' => Post::class,
     *     'url' => 'posts',
     *     'labels' => ['Посты', 'Пост', 'Поста'],
     *     'menuIcon' => 'newsletter',
     * ],
     * ```
     *
     * @var array
     */
    public $modelConfig;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function bindActionParams($action, $params)
    {
        $args = parent::bindActionParams($action, $params);
        if (count($args) === 0) {
            return $args;
        }

        if (!isset($this->module->modelsConfig[$args[0]])) {
            throw new NotFoundHttpException();
        }
        $this->modelConfig = $this->module->modelsConfig[$args[0]];
        /** @var ActiveRecord $model */
        $model = new $this->modelConfig['class'];
        if ($model->getBehavior('crudView') === null) {
            $hasBehavior = false;
            foreach ($model->getBehaviors() as $behavior) {
                if ($behavior instanceof CrudViewBehavior) {
                    $hasBehavior = true;
                    break;
                }
            }
            if (!$hasBehavior) {
                throw new InvalidConfigException(
                    "Model '{$this->modelConfig['class']}' must have 'CrudViewBehavior' behavior."
                );
            }
        }

        $args[0] = $this->modelConfig['class'];
        return $args;
    }

    /**
     * @inheritdoc
     */
    public function getViewPath()
    {
        return \Yii::getAlias('@ylab/administer/views');
    }

    /**
     * Show default module page.
     *
     * @return string
     */
    public function actionDefault()
    {
        return $this->render('default');
    }

    /**
     * Lists all models.
     *
     * @param string $modelClass
     * @return string
     */
    public function actionIndex($modelClass)
    {
        $model = new $modelClass();
        $dataProvider = new ActiveDataProvider(['query' => $modelClass::find()]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'model' => $model,
            'title' => $this->modelConfig['labels'][0],
            'breadcrumbs' => ViewHelper::getBreadcrumbs('index'),
            'buttons' => ViewHelper::getButtons('index', $this->modelConfig['url']),
            'columns' => $this->getAllColumns($model),
        ]);
    }

    /**
     * Displays a single model.
     *
     * @param string $modelClass
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($modelClass, $id)
    {
        $model = $this->findModel($modelClass, $id);
        return $this->render('view', [
            'model' => $model,
            'title' => \Yii::t('ylab/administer', 'View') . " {$this->modelConfig['labels'][2]} #$id",
            'breadcrumbs' => ViewHelper::getBreadcrumbs(
                'view',
                $this->modelConfig['url'],
                $this->modelConfig['labels'][0],
                $id
            ),
            'buttons' => ViewHelper::getButtons('view', $this->modelConfig['url'], $id),
            'columns' => $this->getAllColumns($model),
        ]);
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $modelClass
     * @return string|Response
     */
    public function actionCreate($modelClass)
    {
        /** @var $model \yii\db\ActiveRecord */
        $model = new $modelClass();
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([
                'view',
                'modelClass' => $this->modelConfig['url'],
                'id' => $model->id,
            ]);
        }

        return $this->render('create', [
            'form' => $model->renderForm(),
            'title' => \Yii::t('ylab/administer', 'Create') . " {$this->modelConfig['labels'][1]}",
            'breadcrumbs' => ViewHelper::getBreadcrumbs(
                'create',
                $this->modelConfig['url'],
                $this->modelConfig['labels'][0]
            ),
            'buttons' => ViewHelper::getButtons('create', $this->modelConfig['url']),
        ]);
    }

    /**
     * Updates an existing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $modelClass
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($modelClass, $id)
    {
        $model = $this->findModel($modelClass, $id);
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([
                'view',
                'modelClass' => $this->modelConfig['url'],
                'id' => $id,
            ]);
        }
        return $this->render('update', [
            'form' => $model->renderForm(),
            'title' => \Yii::t('ylab/administer', 'Update') . " {$this->modelConfig['labels'][1]} #$id",
            'breadcrumbs' => ViewHelper::getBreadcrumbs(
                'update',
                $this->modelConfig['url'],
                $this->modelConfig['labels'][0],
                $id
            ),
            'buttons' => ViewHelper::getButtons('update', $this->modelConfig['url'], $id),
        ]);
    }

    /**
     * Deletes an existing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $modelClass
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($modelClass, $id)
    {
        $this->findModel($modelClass, $id)->delete();
        return $this->redirect(['index', 'modelClass' => $this->modelConfig['url']]);
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $modelClass
     * @param int $id
     * @return ActiveRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($modelClass, $id)
    {
        $query = new ActiveQuery($modelClass);
        if (($model = $query->andWhere(['id' => $id])->one()) !== null) {
            return $model;
        }
        throw new NotFoundHttpException();
    }

    /**
     * Get all model columns for shows in GridView in actionIndex.
     *
     * @param ActiveRecord $model
     * @return array
     */
    protected function getAllColumns(ActiveRecord $model)
    {
        $columns = [];
        foreach ($model as $name => $value) {
            if ($value === null || is_scalar($value) || is_callable([$value, '__toString'])) {
                $columns[] = (string)$name;
            }
        }
        return $columns;
    }
}
