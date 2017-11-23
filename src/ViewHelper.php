<?php

namespace ylab\administer;

use yii\helpers\Html;

class ViewHelper
{
    /**
     * @param $action
     * @param null|string $url
     * @param null|string $name
     * @param null|int $id
     * @return array
     */
    public static function getBreadcrumbs($action, $url = null, $name = null, $id = null)
    {
        if ($action === 'index') {
            return [$name];
        }

        $breadcrumbs = [];
        $breadcrumbs[] = ['label' => $name, 'url' => ['index', 'modelClass' => $url]];
        if ($action === 'create') {
            $breadcrumbs[] = \Yii::t('ylab/administer', 'Create');
        } else {
            if ($action === 'update') {
                $breadcrumbs[] = [
                    'label' => "#$id",
                    'url' => ['view', 'modelClass' => $url, 'id' => $id],
                ];
                $breadcrumbs[] = \Yii::t('ylab/administer', ucwords($action));
            } else {
                $breadcrumbs[] = "#$id";
            }
        }

        return $breadcrumbs;
    }

    /**
     * @param string $action
     * @param string $url
     * @param null|int $id
     * @return array
     */
    public static function getButtons($action, $url, $id = null)
    {
        switch ($action) {
            case 'create':
                return [
                    'index' => static::getIndexButton($url),
                ];
            case 'update':
                return [
                    'index' => static::getIndexButton($url),
                    'create' => static::getCreateButton($url),
                    'view' => static::getViewButton($url, $id),
                    'delete' => static::getDeleteButton($url, $id),
                ];
            case 'view':
                return [
                    'index' => static::getIndexButton($url),
                    'create' => static::getCreateButton($url),
                    'update' => static::getUpdateButton($url, $id),
                    'delete' => static::getDeleteButton($url, $id),
                ];
            case 'index':
            default:
                return [
                    'create' => static::getCreateButton($url),
                ];
        }
    }

    /**
     * @param string$url
     * @return string
     */
    protected static function getIndexButton($url)
    {
        return Html::a(
            \Yii::t('ylab/administer', 'Index'),
            ['index', 'modelClass' => $url],
            ['class' => 'btn btn-primary btn-right btn-flat glyphicon-list']
        );
    }

    /**
     * @param string $url
     * @return string
     */
    protected static function getCreateButton($url)
    {
        return Html::a(
            \Yii::t('ylab/administer', 'Create'),
            ['create', 'modelClass' => $url],
            ['class' => 'btn btn-success btn-right btn-flat glyphicon-plus']
        );
    }

    /**
     * @param string $url
     * @param int $id
     * @return string
     */
    protected static function getUpdateButton($url, $id)
    {
        return Html::a(
            \Yii::t('ylab/administer', 'Update'),
            ['update', 'modelClass' => $url, 'id' => $id],
            ['class' => 'btn btn-success btn-flat glyphicon-pencil']
        );
    }

    /**
     * @param string $url
     * @param int $id
     * @return string
     */
    protected static function getViewButton($url, $id)
    {
        return Html::a(
            \Yii::t('ylab/administer', 'View'),
            ['view', 'modelClass' => $url, 'id' => $id],
            ['class' => 'btn btn-primary btn-flat glyphicon-eye-open']
        );
    }

    /**
     * @param string $url
     * @param int $id
     * @return string
     */
    protected static function getDeleteButton($url, $id)
    {
        return Html::a(
            \Yii::t('ylab/administer', 'Delete'),
            ['delete', 'modelClass' => $url, 'id' => $id],
            [
                'class' => 'btn btn-danger btn-flat glyphicon-trash',
                'data' => [
                    'confirm' => \Yii::t('ylab/administer', 'Are you sure you want to delete this item?'),
                    'method' => 'POST',
                ],
            ]
        );
    }
}
