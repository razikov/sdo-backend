<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\search\ObjectSearch;

class ObjectController extends Controller
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
        $filterModel = new ObjectSearch();
        $dataProvider = $filterModel->search(\Yii::$app->request->queryParams);
        
        return $this->render('list', [
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
}
