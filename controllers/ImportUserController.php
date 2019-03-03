<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\ImportUser;
use app\models\ImportUserForm;
use app\models\Role;

class ImportUserController extends Controller
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
                        'actions' => ['list', 'create', 'view', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    public function actionList()
    {
        $dp = new ActiveDataProvider([
            'query' => ImportUser::find()->orderBy(['id' => SORT_DESC])
        ]);
        
        return $this->render('list', [
            'dataProvider' => $dp,
        ]);
    }
    
    public function actionCreate($id = null)
    {
        $availableRoles = Role::getList();
        
        if ($id) {
            $model = ImportUserForm::find()->where(['id' => $id])->one();
        } else {
            $model = new ImportUserForm();
        }
        $model->load(Yii::$app->getRequest()->post());
        if ($model->step == ImportUserForm::STAGE_GENERATE_FILE && $model->validate()) {
            $saveModel = new ImportUser();
            $saveModel->load($model->attributes, '');
            $saveModel->step = ImportUser::STAGE_EXPORT;
            if ($saveModel->save()) {
                $this->redirect(Url::to(['', 'id' => $saveModel->id]));
            };
        }
        
        return $this->render('create', [
            'model' => $model,
            'roles' => $availableRoles,
        ]);
    }
    
    public function actionView($id)
    {
        $model = ImportUser::find()->where(['id' => $id])->one();
        
        if (!$model) {
            throw new NotFoundHttpException();
        }
        
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = ImportUser::find()->where(['id' => $id])->one();
        
        if (!$model) {
            throw new NotFoundHttpException();
        }
        
        $model->delete();
        
        $this->redirect(Url::to('list'));
    }
    
}
