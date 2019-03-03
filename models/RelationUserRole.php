<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class RelationUserRole extends ActiveRecord
{

    public static function getDb()
    {
        return Yii::$app->get('ilias');
    }
    
    public static function tableName()
    {
        return 'rbac_ua';
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
