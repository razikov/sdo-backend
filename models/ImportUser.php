<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use app\models\Upload;
use yii\behaviors\TimestampBehavior;

class ImportUser extends ActiveRecord
{
    const STAGE_IMPORT = 1;
    const STAGE_EDIT = 2;
    const STAGE_GENERATE_FILE = 3;
    const STAGE_EXPORT = 4;
    
    public static function tableName()
    {
        return 'custom_import_user';
    }
    
    public function attributeLabels()
    {
        return [
            'step' => Yii::t('app', 'Этап'),
            'name' => Yii::t('app', 'Название'),
            'created_at' => Yii::t('app', 'Дата'),
            'upload_id' => Yii::t('app', 'Импорт XLS'),
            'upload_id_xml' => Yii::t('app', 'Экспорт XML'),
            'upload_id_xls' => Yii::t('app', 'Экспорт XLS'),
        ];
    }
    
    public function setRoleIds(array $value)
    {
        $this->role_ids = implode(',', $value);
        return $this;
    }
    
    public function getRoleIds()
    {
        return explode(',', $this->role_ids);
    }
    
    public function rules()
    {
        return [
            [['users_json', 'upload_id', 'step', 'role_ids', 'upload_id_xml', 'upload_id_xls', 'name'], 'required'],
            [['upload_id', 'upload_id_xml', 'upload_id_xls'], 'number'],
            [['users_json'], 'default', 'value' => ''],
            [['upload_id'], 'default', 'value' => null],
            [['step'], 'default', 'value' => self::STAGE_IMPORT],
        ];
    }
    
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }
    
    public function getStepName()
    {
        $availableName = [
            self::STAGE_IMPORT => 'Импорт пользователей',
            self::STAGE_EDIT => 'Редактирование пользователей',
            self::STAGE_GENERATE_FILE => 'Подготовка файлов экспорта',
            self::STAGE_EXPORT => 'Экспорт пользователей',
        ];
        return ArrayHelper::getValue($availableName, $this->step, 'Не известен');
    }
    
    public function getUpload()
    {
        return $this->hasOne(Upload::class, ['id' => 'upload_id']);
    }
    public function getUploadXls()
    {
        return $this->hasOne(Upload::class, ['id' => 'upload_id_xls']);
    }
    public function getUploadXml()
    {
        return $this->hasOne(Upload::class, ['id' => 'upload_id_xml']);
    }
    
}
