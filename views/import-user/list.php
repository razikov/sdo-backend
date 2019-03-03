<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<?= GridView::widget([
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => null,
    'columns' => [
        [
            'label' => '#',
            'attribute' => 'id',
        ],
        [
            'attribute' => 'upload_id',
            'format' => 'raw',
            'value' => function ($item) {
                return Html::a($item->upload->filename, $item->upload->url);
            }
        ],
        [
            'attribute' => 'upload_id_xls',
            'format' => 'raw',
            'value' => function ($item) {
                return Html::a($item->uploadXls->filename, $item->uploadXls->url);
            }
        ],
        [
            'attribute' => 'upload_id_xml',
            'format' => 'raw',
            'value' => function ($item) {
                return Html::a($item->uploadXml->filename, $item->uploadXml->url);
            }
        ],
        [
            'class' => yii\grid\ActionColumn::class,
            'template' => '{view} {delete}',
            'visibleButtons' => [
                'view' => function ($model) {
                    return true;
                },
                'create' => function ($model) {
                    return true;
                },
                'delete' => function ($model) {
                    return true;
                },
            ],
            'buttons' => [
                'view' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', $url,
                            ['class' => '', 'title' => Yii::t('app', 'Просмотр')]
                        );
                },
                'create' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url,
                            ['class' => '', 'title' => Yii::t('app', 'Редактировать')]
                        );
                },
                'delete' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url,
                            ['class' => '', 'title' => Yii::t('app', 'Удалить')]
                        );
                },
            ],
        ],
    ],
]); ?>