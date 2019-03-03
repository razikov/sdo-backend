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
        var_dump('stop');exit;
        
        
        return $this->render('list', [
            'items' => $items,
        ]);
    }
    
}
