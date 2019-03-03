<?php

use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;

?>
<div class="btn-group">
  <?= Html::a("Добавить", Url::to('create'), ['class' => 'btn btn-default']); ?>
  <?= Html::a("Добавить файл импорта", Url::to('import'), ['class' => 'btn btn-default']); ?>
</div>


<?= GridView::widget([
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $filterModel,
    'columns' => [
        'login',
        'password',
        [
            'class' => yii\grid\ActionColumn::class,
            'template' => '{update} {delete}',
            'visibleButtons' => [
                'update' => function ($model) {
                    return true;
                },
                'delete' => function ($model) {
                    return true;
                },
            ],
            'buttons' => [
                'update' => function ($url, $model) {
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