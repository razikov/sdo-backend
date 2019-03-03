<?php

namespace app\models;

use Yii;

class UserIliasSessionStat extends \yii\db\ActiveRecord
{

    public static function getDb()
    {
        return Yii::$app->get('ilias');
    }
    
    public static function tableName()
    {
        return 'usr_session_stats';
    }
    
    public function attributeLabels()
    {
        return [
            'slot_begin' => Yii::t('app', 'slot_begin'),
            'slot_end   ' => Yii::t('app', 'slot_end'),
            'active_min' => Yii::t('app', 'active_min'),
            'active_max' => Yii::t('app', 'active_max'),
            'active_avg' => Yii::t('app', 'active_avg'),
            'active_end' => Yii::t('app', 'active_end'),
            'opened' => Yii::t('app', 'opened'),
            'closed_manual' => Yii::t('app', 'closed_manual'),
            'closed_expire' => Yii::t('app', 'closed_expire'),
            'closed_idle' => Yii::t('app', 'closed_idle'),
            'closed_idle_first' => Yii::t('app', 'closed_idle_first'),
            'closed_limit' => Yii::t('app', 'closed_limit'),
            'closed_login' => Yii::t('app', 'closed_login'),
            'max_sessions' => Yii::t('app', 'max_sessions'),
            'closed_misc' => Yii::t('app', 'closed_misc'),
        ];
    }
    
}
