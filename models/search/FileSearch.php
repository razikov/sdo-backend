<?php

namespace app\models\search;

use yii\data\ActiveDataProvider;
use app\models\File;

class FileSearch extends File
{
    public function rules()
    {
        return [
            [[], 'safe'],
        ];
    }
    public function search($filter)
    {
        $this->load($filter);
        
        $query = self::find();
//        $query->andFilterWhere(['obj_id' => $this->obj_id]);
//        $query->andFilterWhere(['type' => $this->type]);
//        $query->andFilterWhere(['like', 'title', $this->title]);
//        $query->andFilterWhere(['like', 'description', $this->description]);

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [],
        ]);
    }
}
