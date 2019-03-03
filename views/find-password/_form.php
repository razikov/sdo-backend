<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$form = ActiveForm::begin([
    
]);
?>
<fieldset>
    <?= $form->field($model, 'login'); ?>
    <?= $form->field($model, 'password'); ?>
</fieldset>
<div class="btn-group">
    <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'Добавить') : Yii::t('app', 'Сохранить'),
            ['class' => 'btn btn-sm btn-primary', 'id' => 'js-save-form']
        ); ?>
    <?= Html::a(
            Yii::t('app', 'Отменить'),
            Url::to('list'),
            ['class' => 'btn btn-sm btn-default']
        ); ?>
</div>
<?php
ActiveForm::end();