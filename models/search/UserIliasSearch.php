<?php

namespace app\models\search;

use app\models\UserIlias;
use yii\data\ActiveDataProvider;

class UserIliasSearch extends UserIlias
{
    public function rules()
    {
        return [
            [['login', 'firstname', 'lastname', 'email'], 'safe'],
        ];
    }
    public function search($filter)
    {
        $this->load($filter);
        
        $query = self::find();
        $query->andFilterWhere(['like', 'login', $this->login]);
        $query->andFilterWhere(['like', 'firstname', $this->firstname]);
        $query->andFilterWhere(['like', 'lastname', $this->lastname]);
        $query->andFilterWhere(['like', 'email', $this->email]);

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [],
        ]);
    }
}
