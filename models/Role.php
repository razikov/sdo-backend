<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Role extends ActiveRecord
{
    const OBJECT_TYPE = 'role';

    public static function getDb()
    {
        return Yii::$app->get('ilias');
    }
    
    public static function tableName()
    {
        return 'role_data';
    }
    
    public function attributeLabels()
    {
        return [
        ];
    }
    
    public function rules()
    {
        return [
            ['title', 'validateTitle'],
        ];
    }
    
    public function validateTitle($attribute, $params)
    {
        if (strpos($this->title, 'il_') === true) {
            $this->addError($attribute, 'префикс "il_" в название роли зарезервировано, смените название');
        }
    }
    
    public static function getList()
    {
        return static::find()
                ->select([ObjectData::tableName().'.title', 'role_id'])
                ->leftJoin(ObjectData::tableName(), ObjectData::tableName().'.obj_id = role_id')
                ->indexBy('role_id')
                ->column();
    }
    
    public function getObjectData()
    {
        return $this->hasOne(ObjectData::class, ['obj_id' => 'role_id'])
            ->andWhere(['obj_id' => $this->role_id]);
    }
    
    public function hasGlobal()
    {
        return Yii::$app->get('ilias')
                ->createCommand("SELECT 1 FROM ilias.rbac_fa where rol_id = :role_id and parent = 8 and assign = 'y'", [':role_id' => $this->role_id])
                ->queryScalar();
    }
    
    public function getIliasId()
    {
        //il_0_role_ID
        //il_crs_member_ID
        //il_grp_admin_ID
        return sprintf("il_0_%s_%s", self::OBJECT_TYPE, $this->role_id);
    }
    
}
