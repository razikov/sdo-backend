<?php

namespace app\models\tree;

class Tree
{
    public $tree;
    public $id;
    public $parent_id;
    public $lft;
    public $rgt;
    public $depth;
    public $path;
    
    public $typeTree;
    public $treeId = 1;


    public function load($data)
    {
        $this->id = \yii\helpers\ArrayHelper::getValue($data, 'child');
        $this->name = sprintf("id[#%d] type[#%s] %s", 
                $this->id,
                \yii\helpers\ArrayHelper::getValue($data, 'type'),
                \yii\helpers\ArrayHelper::getValue($data, 'title')
            );
        $this->parent_id = \yii\helpers\ArrayHelper::getValue($data, 'parent');
        $this->lft = \yii\helpers\ArrayHelper::getValue($data, 'lft');
        $this->rgt = \yii\helpers\ArrayHelper::getValue($data, 'rgt');
        $this->lvl = \yii\helpers\ArrayHelper::getValue($data, 'depth');
    }
    
}
