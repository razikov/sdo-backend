<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="alert alert-info">
    <b>Подсказки:</b>
    <?= Html::ul([
        'Ожидается что будет загружен xls файл, в котором в 3 столбце будет логин, а в 4 пароль.',
        'Логины уникальны, поэтому если будут встречены повторы, значения паролей будут пропускаться.'
    ]);?>
</div>
<?php
$form = ActiveForm::begin([
    'options' => [
        'enctype' => "multipart/form-data",
    ],
]);
?>
<fieldset>
    <div class="form-group<?= $errors ? ' has-error' : ''; ?>">
        <label class="control-label"></label>
        <?= Html::fileInput('file', null, ['id' => 'js-file']); ?>

        <div class="help-block help-block-error"><?= $errors ? Html::ul($errors) : ''; ?></div>
    </div>
</fieldset>

<div class="btn-group">
    <?= Html::submitButton(
        Yii::t('app', 'Загрузить'),
        ['class' => 'btn btn-sm btn-primary']
    ); ?>
    <?= Html::a(
        Yii::t('app', 'Отменить'),
        Url::to('list'),
        ['class' => 'btn btn-sm btn-default']
    ); ?>
</div>
<?php
ActiveForm::end();