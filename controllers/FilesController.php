<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\search\FileSearch;

class FilesController extends Controller
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
        $filterModel = new FileSearch();
        $dataProvider = $filterModel->search(\Yii::$app->request->queryParams);
        
        return $this->render('list', [
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
}
