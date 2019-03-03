<?php 

use app\helpers\XmlHelper;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use app\widgets\Select;

$this->registerJs('
    $("body").on("change", "#js-file", function() {
        let element = $(this);
        let files = document.querySelector("#js-file").files;
        for (var index = 0; index < files.length; index++) {
            let item = files.item(index);
            let formData = new FormData();
            formData.append("file", item);
            $.ajax({
                url: "/site/upload",
                data: formData,
                processData: false,
                contentType: false,
                type: "POST"
            }).done(response => {
                if (response.uploaded) {
                    $("#form").submit();
                } else {
                    let msg = "Не удалось загрузить файл. Ошибки приложения: ";
                    for (let attr in response.errors) {
                        response.errors[attr].forEach((error) => {
                            msg = msg + error + " ";
                        });
                    }
                    alert(msg);
                }
            }).fail((jqXHR, textStatus, errorThrown) => {
                console.log(jqXHR.responseText, textStatus);
                alert("Не удалось загрузить файл. Ошибка сервера: " + jqXHR.status + " " + errorThrown);
            });
        }
    });
    $("body").on("click", "#js-save-form", function() {
        $("#import_step").val(3);
    });
    $("body").on("change", "#js-select-upload-id", function() {
        $("#form").submit();
    });
', \yii\web\View::POS_END);
?>

<?php
$form = ActiveForm::begin([
    'id' => 'form'
]);
?>
<?= $form->field($model, 'usersJson')->hiddenInput()->label(false); ?>
<?= $form->field($model, 'step')->hiddenInput(['id' => 'import_step'])->label(false); ?>
<div class="form-group">
    <label class="control-label">Этап:</label>
    <div><?= $model->stepName; ?></div>

    <p class="help-block help-block-error"></p>
</div>
<?php if ($model->step == 1): ?>
    <div class="form-group">
        <label class="control-label">Загрузить новый:</label>
        <div><?= Html::fileInput('file', null, ['id' => "js-file"]); ?></div>
    </div>
    <?= $form->field($model, 'upload_id')->widget(
        Select::class,
        [
            'options' => [
                'id' => 'js-select-upload-id',
                'class' => 'form-control ',
                'data-style' => 'btn-default',
                'data-width' => '100%',
                'data-live-search' => 1,
                'prompt' => Yii::t('app', 'Не выбран'),
            ],
            'items' =>  yii\helpers\ArrayHelper::map(\app\models\Upload::find()->orderBy(['id' => SORT_DESC])->all(), 'id', function ($item) {
                return sprintf("[#%d] %s", $item->id, $item->filename);
            }),
        ]
    )->label("Выбрать существующий:"); ?>
<?php elseif ($model->step == 2 || $model->step == 3): ?>
    <?= $form->field($model, 'upload_id')->hiddenInput(['id' => 'import_upload_id'])->label(false); ?>
    <div class="form-group">
        <label class="control-label">Загружен:</label>
        <div><?= Html::a($model->upload->filename, $model->upload->url); ?></div>
    </div>
    <div>
        <?= $form->field($model, 'roleIds')->widget(
            Select::class,
            [
                'options' => [
                    'class' => 'form-control ',
                    'data-style' => 'btn-default',
                    'data-width' => '100%',
                    'data-live-search' => 1,
                    'multiple' => 1,
                ],
                'items' =>  $roles,
            ]
        ); ?>
    </div>
    <br>
    <?php foreach ($model->users as $i => $user): ?>
    <div class="row">
        <div class="col-md-1"><?= $i+1 ?>.</div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-xs-4" style="text-align: right;"><?= Html::label('ФИО: ') ?></div>
                <div class="col-xs-8"><?= $user->fullName ?></div>
            </div>
            <div class="row">
                <div class="col-xs-4" style="text-align: right;"><?= Html::label('Email: ') ?></div>
                <div class="col-xs-8"><?= $user->email ?></div>
            </div>
            <div class="row">
                <div class="col-xs-4" style="text-align: right;"><?= Html::label('ОУ: ') ?></div>
                <div class="col-xs-8"><?= $user->institution ?></div>
            </div>
            <div class="row">
                <div class="col-xs-4" style="text-align: right;"><?= Html::label('Логин: ', "login_{$i}") ?></div>
                <div class="col-xs-8"><?= Html::textInput("login[{$i}]", $user->login) ?></div>
            </div>
            <div class="row">
                <div class="col-xs-4" style="text-align: right;"><?= Html::label('Пароль: ', "login_{$i}") ?></div>
                <div class="col-xs-8"><?= Html::textInput("rawPassword[{$i}]", $user->rawPassword) ?></div>
            </div>
        </div>
        <div class="col-md-5" style="color:red">
            <?= Html::errorSummary($user) ?>
        </div>
    </div>
    <hr>
    <?php endforeach; ?>
    <?= Html::submitButton(
        Yii::t('app', 'Проверить'),
        ['class' => 'btn btn-sm btn-primary']
    ); ?>
    <?= Html::submitButton(
        Yii::t('app', 'Сохранить'),
        ['class' => 'btn btn-sm btn-primary', 'id' => 'js-save-form']
    ); ?>
<?php elseif ($model->step == 4): ?>
    <div class="form-group">
        <label class="control-label">Скачать XLS:</label>
        <div><?= Html::a($model->uploadXls->filename, $model->uploadXls->url); ?></div>
    </div>
    <div class="form-group">
        <label class="control-label">Скачать XML:</label>
        <div><?= Html::a($model->uploadXml->filename, $model->uploadXml->url); ?></div>
    </div>
<?php endif; ?>
<?php
ActiveForm::end();
?>