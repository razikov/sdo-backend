<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use app\helpers\XmlHelper;

class ImportUserForm extends ImportUser
{
    const SCENARIO_IMPORT_FORM = 'import-form';
    
    public $users = [];

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'roleIds' => Yii::t('app', 'Роли'),
        ]);
    }
    
    public function init()
    {
        $this->step = self::STAGE_IMPORT;
        $this->roleIds = [276];
        $this->users_json = '';
        $this->scenario = self::SCENARIO_IMPORT_FORM;
    }
    
    public function rules()
    {
        if ($this->scenario == self::SCENARIO_IMPORT_FORM) {
            $rules = [
                [
                    ['users', 'name', 'upload_id', 'roleIds', 'step', 'users_json', 'upload_id_xls', 'upload_id_xml'],
                    'safe',
                    'on' => self::SCENARIO_IMPORT_FORM
                ],
                [
                    ['name'],
                    'required',
                    'when' => function() {
                        return $this->step != self::STAGE_IMPORT;
                    },
                    'on' => self::SCENARIO_IMPORT_FORM
                ],
                [
                    ['name'],
                    'string', 'min' => 4,
                    'when' => function() {
                        return $this->step == self::STAGE_EDIT;
                    },
                    'on' => self::SCENARIO_IMPORT_FORM
                ],
                [
                    ['roleIds'],
                    'required',
                    'when' => function() {
                        return $this->step == self::STAGE_EDIT;
                    },
                    'on' => self::SCENARIO_IMPORT_FORM
                ],
                [
                    ['users', 'upload_id', 'roleIds', 'step', 'users_json', 'upload_id_xls', 'upload_id_xml'],
                    'required',
                    'when' => function() {
                        return $this->step == self::STAGE_GENERATE_FILE;
                    },
                    'whenClient' => 'function(attribute, value) {
                        return $("#import_step") == ' . self::STAGE_GENERATE_FILE . ';
                    }',
                    'on' => self::SCENARIO_IMPORT_FORM
                ],
            ];
        } else {
            $rules = parent::rules();
        }
        return $rules;
    }
    
    public function load($data, $formName = NULL)
    {
        $return = parent::load($data, $formName);
        $this->sinhronizeUsersFromJsonUsers();
        if ($this->step == self::STAGE_IMPORT && $this->setUsersFromXML()) {
            $this->step = self::STAGE_EDIT;
        }
        if ($this->users) {
            $logins = ArrayHelper::getValue($data, 'login');
            $rawPasswords = ArrayHelper::getValue($data, 'rawPassword');
            foreach ($this->users as $key => &$user) {
                if (isset($logins[$key])) {
                    $user->login = $logins[$key];
                }
                if (isset($rawPasswords[$key])) {
                    $user->rawPassword = $rawPasswords[$key];
                    $user->passwd = md5($rawPasswords[$key]);
                }
                if ($this->roleIds) {
                    $user->role_ids = $this->roleIds;
                }
                $user->validate();
            }
            $this->sinhronizeJsonUsersFromUsers();
        }
        if ($this->step == self::STAGE_GENERATE_FILE) {
            $users = $this->users;
            $path = Yii::getAlias('@runtime') . '/' . uniqid('xls_render_').'.xls';
            XmlHelper::renderExcel($users, $path);
            $result = $this->uploadFile($path);
            if ($result['uploaded']) {
                $this->upload_id_xls = $result['model']->id;
            }
            $path = Yii::getAlias('@runtime') . '/' . uniqid('xml_render_').'.xml';
            XmlHelper::renderXml($users, $path);
            $result = $this->uploadFile($path);
            if ($result['uploaded']) {
                $this->upload_id_xml = $result['model']->id;
            }
            foreach($users as $user) {
                if (!$user->validate()) {
                    continue;
                }
                $model = FindPassword::find()->where(['login' => $user->login])->one();
                if (!$model) {
                    $model = new FindPassword();
                    $model->load([
                        'login' => $user->login,
                        'password' => $user->rawPassword,
                    ], '');
                    $model->validate() && $model->save();
                }
            }
        }
        return $return;
    }
    
    protected function uploadFile($path)
    {
        $model = new Upload();
        
        $mimeType = FileHelper::getMimeType($path);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $newFileName = sha1_file($path).'.'.$extension;

        $fileStorage = Yii::$app->get('storageContainer')->getFileStorageByUploadType(\app\models\Upload::TYPE_LOCAL);
        $model->filename = $fileStorage->upload(
            $path,
            $newFileName,
            $mimeType
        );
        @unlink($path);

        if ($model->save()) {
            return ['uploaded' => true, 'url' => $fileStorage->getUrl($model), 'model' => $model];
        } else {
            return ['uploaded' => false, 'errors' => $model->getErrors()];
        }
    }


    public function setUsersFromXML()
    {
        if ($this->users_json == '' && $this->upload) {
            $data = XmlHelper::read(Yii::getAlias('@webroot').$this->upload->url);
            $users = [];
            foreach ($data as $row) {
                $user = new UserIlias();
                $pwd = ArrayHelper::getValue($row, ['password'], '');
                $user->load([
                    'firstname' => trim(sprintf('%s %s', 
                            ArrayHelper::getValue($row, ['name'], ''),
                            ArrayHelper::getValue($row, ['otch'], '')
                        )),
                    'lastname' => ArrayHelper::getValue($row, ['fam']),
                    'email' => ArrayHelper::getValue($row, ['email']),
                    'phone_office' => ArrayHelper::getValue($row, ['phone']),
                    'login' => ArrayHelper::getValue($row, ['login']),
                    'rawPassword' => $pwd,
                    'passwd' => $pwd ? md5($pwd) : $pwd,
                    'institution' => ArrayHelper::getValue($row, ['ou']),
                ], '');
                $user->validate();
                $users[] = $user;
            }
            $this->users = $users;
            $this->sinhronizeJsonUsersFromUsers();
            return true;
        }
        return false;
    }
    
    public function sinhronizeUsersFromJsonUsers()
    {
        if ($this->users_json) {
            $users = [];
            $usersJson = json_decode($this->users_json);
            foreach ($usersJson as $key => $user) {
                $model = new UserIlias((array)$user);
                $model->validate();
                $users[$key] = $model;
            }
            $this->users = $users;
            return true;
        }
        
        return false;
    }
    
    public function sinhronizeJsonUsersFromUsers()
    {
        $result = [];
        foreach ($this->users as $key => $user) {
            $result[$key] = array_merge(
                ['role_ids' => $user->role_ids, 'rawPassword' => $user->rawPassword],
                array_filter($user->attributes, function($item) {
                    if (!is_null($item)) {
                        return $item;
                    };
                })
            );
        }
        $this->users_json = json_encode($result);
        return true;
    }
    
}
