<?php

use yii\grid\GridView;

?>

<?= GridView::widget([
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $filterModel,
    'columns' => [
        'obj_id',
        'type',
        'title',
        'description',
        'create_date',
        'last_update',
    ],
]); ?>