<?php

use yii\grid\GridView;

?>

<?= GridView::widget([
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $filterModel,
    'columns' => [
        'file_id',
        'file_name',
        'file_type',
        'file_size',
        'version',
        'f_mode',
        'rating',
    ],
]); ?>