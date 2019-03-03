<?php

namespace app\models\search;

use app\models\UserIliasSessionStat;
use yii\data\ActiveDataProvider;

class UserIliasSessionStatSearch extends UserIliasSessionStat
{
    public function rules()
    {
        return [
            [
                [
                    'slot_begin',
                    'slot_end',
                ], 
                'safe'
            ],
        ];
    }
    public function search($filter)
    {
        $this->load($filter);
        
        $query = self::find();
//        $query->andFilterWhere(['like', 'login', $this->login]);
//        $query->andFilterWhere(['like', 'firstname', $this->firstname]);
//        $query->andFilterWhere(['like', 'lastname', $this->lastname]);
//        $query->andFilterWhere(['like', 'email', $this->email]);

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [],
        ]);
    }
}
