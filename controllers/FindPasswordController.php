<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\FindPassword;
use app\models\search\FindPasswordSearch;
use app\helpers\XmlHelper;

class FindPasswordController extends Controller
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
                        'actions' => ['list', 'import', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ],
            ],
        ];
    }
    
    public function actionList()
    {
        $filterModel = new FindPasswordSearch();
        $dataProvider = $filterModel->search(\Yii::$app->request->queryParams);
        
        return $this->render('list', [
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionImport()
    {
        $errors = null;
        if (Yii::$app->request->isPost) {
            $file = UploadedFile::getInstanceByName('file');
            if ($file) {
                $arrUsers = XmlHelper::readUserList($file->tempName);
                foreach ($arrUsers as $arr) {
                    $model = FindPassword::find()->where(['login' => $arr['login']])->one();
                    if (!$model) {
                        $model = new FindPassword();
                        $model->load($arr, '');
                        $model->save();
                    } else {
                        // В существующей: пропустить, добавить или заменить значение
//                        $model->password = $arr['password'];
//                        $model->save();
                    }
                }
                // тут можно отправить флешку, сколько добавлено? сколько импортировано?
                $this->redirect(Url::to('list'));
            } else {
                $errors[] = "Не удалось загрузить файл";
            }
        }
        
        return $this->render('import', ['errors' => $errors]);
    }
    
    public function actionCreate()
    {
        $model = new FindPassword();
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            $this->redirect(Url::to('list'));
        }
        
        return $this->render('_form', [
            'model' => $model,
        ]);
    }
    
    public function actionUpdate($id)
    {
        $model = FindPassword::find()->where(['id' => $id])->one();
        
        if (!$model) {
            throw new NotFoundHttpException("Запись не найдена");
        }
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            $this->redirect(Url::to('list'));
        }
        
        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = FindPassword::find()->where(['id' => $id])->one();
        
        if (!$model) {
            throw new NotFoundHttpException("Запись не найдена");
        }
        
        $model->delete();
        $this->redirect(Url::to('list'));
    }
    
}
