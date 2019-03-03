<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\search\UserIliasSessionStatSearch;

class StatisticsController extends Controller
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
                        'actions' => ['session'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionSession()
    {
        $filterModel = new UserIliasSessionStatSearch();
        $dataProvider = $filterModel->search(\Yii::$app->request->queryParams);
        
        return $this->render('session', [
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
}
