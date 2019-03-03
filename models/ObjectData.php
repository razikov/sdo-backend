<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class ObjectData extends ActiveRecord
{

    public static function getDb()
    {
        return Yii::$app->get('ilias');
    }
    
    public static function tableName()
    {
        return 'object_data';
    }
    
    public function attributeLabels()
    {
        return [
        ];
    }
    
    public function rules()
    {
        return [
        ];
    }
    
}
