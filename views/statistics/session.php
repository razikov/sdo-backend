<?php

use yii\grid\GridView;


?>

<?= GridView::widget([
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => null,//$filterModel,
    'columns' => [
        [
            'attribute' => 'slot_begin',
            'value' => function($item) {
                return date("d.m.Y H:i:s", $item->slot_begin) . " ({$item->slot_begin})";
            }
        ],
        [
            'attribute' => 'slot_end',
            'value' => function($item) {
                return date("d.m.Y H:i:s", $item->slot_end) . " ({$item->slot_end})";
            }
        ],
        'active_min',
        'active_max',
        'active_avg',
        'active_end',
        'opened',
        [
            'header' => 'closed',
            'value' => function($item) {
                return array_sum([
                    $item->closed_manual,
                    $item->closed_expire,
                    $item->closed_idle,
                    $item->closed_idle_first,
                    $item->closed_limit,
                    $item->closed_login,
                    $item->closed_misc,
                ]);
            }
        ],
//        'closed_manual',
//        'closed_expire',
//        'closed_idle',
//        'closed_idle_first',
//        'closed_limit',
//        'closed_login',
//        'closed_misc',
        'max_sessions',
    ],
]); ?>