<?php

namespace app\models\search;

use yii\data\ActiveDataProvider;
use app\models\FindPassword;

class FindPasswordSearch extends FindPassword
{
    public function rules()
    {
        return [
            [['login'], 'safe'],
        ];
    }
    public function search($filter)
    {
        $this->load($filter);
        
        $query = self::find();
        $query->andFilterWhere(['like', 'login', $this->login]);
        $query->andFilterWhere(['like', 'password', $this->password]);

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [],
        ]);
    }
}
