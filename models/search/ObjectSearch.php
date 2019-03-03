<?php

namespace app\models\search;

use yii\data\ActiveDataProvider;
use app\models\ObjectData;

class ObjectSearch extends ObjectData
{
    public function rules()
    {
        return [
            [['obj_id', 'type', 'title', 'description'], 'safe'],
        ];
    }
    public function search($filter)
    {
        $this->load($filter);
        
        $query = self::find();
        $query->andFilterWhere(['obj_id' => $this->obj_id]);
        $query->andFilterWhere(['type' => $this->type]);
        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere(['like', 'description', $this->description]);

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [],
        ]);
    }
}
