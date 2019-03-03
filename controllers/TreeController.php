<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\tree\Tree;
use app\models\tree\NestedSetTree;

class TreeController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function () {
                    \Yii::$app->user->loginRequired();
                },
                'rules' => [
                    [
                        'actions' => ['list'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionList()
    {
        $items = [];
        if (Yii::$app->params['useTree'] == 'ns') {
            $ns = new \app\models\tree\NestedSetTree([
                'keyId' => 'child',
                'keyParentId' => 'parent',
            ]);
            $items = $ns->getAll(1);
        } elseif (Yii::$app->params['useTree'] == 'mp') {
            $mp = new \app\models\tree\MaterializedPathTree([
                'keyId' => 'child',
                'keyParentId' => 'parent',
            ]);
            $items = $mp->getAll(1);
        }
        
        return $this->render('list', [
            'items' => $items,
        ]);
    }
    
}
