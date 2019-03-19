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
            'attribute' => 'name',
        ],
        [
            'attribute' => 'created_at',
            'format' => 'raw',
            'value' => function ($item) {
                return Yii::$app->formatter->asDatetime($item->created_at, 'd.MM.yyyy H:mm:ss');
            }
        ],
        [
            'header' => 'Файлы',
            'format' => 'raw',
            'value' => function ($item) {
                return sprintf("[%s]; [%s]; [%s];",
                        Html::a($item->getAttributeLabel('upload_id'), $item->upload->url),
                        Html::a($item->getAttributeLabel('upload_id_xls'), $item->uploadXls->url),
                        Html::a($item->getAttributeLabel('upload_id_xml'), $item->uploadXml->url)
                        );
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