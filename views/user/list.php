<?php

use yii\grid\GridView;

?>

<?= GridView::widget([
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $filterModel,
    'columns' => [
        'usr_id',
        'login',
        'firstname',
        'lastname',
        'validUntil',
        'last_login',
        'email',
//        'second_email',
    ],
]); ?>