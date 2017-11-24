<?php

namespace ylab\administer\helpers;

/**
 * Helper class for generating breadcrumbs.
 */
class BreadcrumbsHelper
{
    /**
     * Create breadcrumbs items config.
     *
     * @param string $action
     * @param null|string $url
     * @param null|string $name
     * @param null|int $id
     * @return array
     */
    public function getBreadcrumbs($action, $url = null, $name = null, $id = null)
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
}
