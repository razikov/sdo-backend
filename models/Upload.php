<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * 
 */
class Upload extends ActiveRecord
{
    const TYPE_LOCAL = 1;

    public static function tableName()
    {
        return 'uploads';
    }

    public function rules()
    {
        return [
            ['type', 'default', 'value' => self::TYPE_LOCAL],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'filename' => Yii::t('app', 'Путь'),
            'created_by' => Yii::t('app', 'Автор'),
            'created_at' => Yii::t('app', 'Создан'),
            'updated_at' => Yii::t('app', 'Изменен'),
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => false,
                'defaultValue' => 1,
            ],
        ];
    }

    public function getUrl()
    {
        $fileStorage = Yii::$app->get('storageContainer')->getFileStorageByUploadType(self::TYPE_LOCAL);

        return $fileStorage->getUrl($this);
    }
    
}
