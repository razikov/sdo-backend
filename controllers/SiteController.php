<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'login', 'error', 'captcha'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'upload', 'twig'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
    
    public function actionUpload()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new \app\models\Upload();
        
        if (Yii::$app->request->isPost) {
            $file = \yii\web\UploadedFile::getInstanceByName('file');
            $mimeType = $file->type;// || FileHelper::getMimeType($this->file->tempName);
            $extension = pathinfo($file->name, PATHINFO_EXTENSION);// || 'undefined';
            $newFileName = sha1_file($file->tempName).'.'. $extension;

            $fileStorage = Yii::$app->get('storageContainer')->getFileStorageByUploadType(\app\models\Upload::TYPE_LOCAL);
            $model->filename = $fileStorage->upload(
                $file->tempName,
                $newFileName,
                $mimeType
            );
            @unlink($file->tempName);
            
            if ($model->save()) {
                return ['uploaded' => true, 'url' => $fileStorage->getUrl($model), 'model' => $model];
            }

            // TODO: в ошибки передать массив сообщений
            return ['uploaded' => false, 'errors' => $model->getErrors()];
        }

        return ['uploaded' => false, 'errors' => 'Должен был быть post запрос'];
    }
    
    public function actionTwig()
    {
        $this->layout = null;
        
        return $this->renderPartial('index.twig');
    }
    
}
