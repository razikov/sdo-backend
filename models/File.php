<?php

namespace app\models;

use Yii;


class File extends \yii\db\ActiveRecord
{
    const OBJECT_TYPE = 'file';

    public static function getDb()
    {
        return Yii::$app->get('ilias');
    }
    
    public static function tableName()
    {
        return 'file_data';
    }
    
    public function attributeLabels()
    {
        return [
            'file_id' => Yii::t('app', '#'),
            'file_name' => Yii::t('app', 'file_name'),
            'file_type' => Yii::t('app', 'file_type'),
            'file_size' => Yii::t('app', 'file_size'),
            'version' => Yii::t('app', 'version'),
            'f_mode' => Yii::t('app', 'f_mode'),
            'rating' => Yii::t('app', 'rating'),
        ];
    }
    
    public function rules()
    {
        return [
            [
                [
                    'file_id',
                    'file_name',
                    'file_type',
                    'file_size',
                    'version',
                    'f_mode',
                    'rating',
                ],
                'safe',
            ],
        ];
    }
    
    public function getObjectData()
    {
        return $this->hasOne(ObjectData::class, ['obj_id' => 'file_id'])
                        ->andWhere(['type' => self::OBJECT_TYPE]);
    }

}
