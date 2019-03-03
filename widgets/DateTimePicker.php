<?php

namespace app\widgets;

use yii\helpers\FormatConverter;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\InputWidget;
use app\assets\JqueryDatePickerAsset;

class DateTimePicker extends InputWidget
{
    public $format;
    public $language;

    public function init()
    {
        parent::init();
        if ($this->format === null) {
            $this->format = FormatConverter::convertDateIcuToPhp(\Yii::$app->formatter->datetimeFormat);
        }
        if ($this->language === null) {
            $this->language = substr(\Yii::$app->language, 0, 2);
        }
        Html::addCssClass($this->options, 'datetimepicker');
    }

    public function run()
    {
        $this->registerClientScript();
        if ($this->hasModel()) {
            echo Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textInput($this->name, $this->value, $this->options);
        }
    }

    public function registerClientScript()
    {
        $view = $this->getView();
        JqueryDatePickerAsset::register($view);
        $params = Json::encode(
            [
                'format' => $this->format,
                'lang' => $this->language,
                'dayOfWeekStart' => 1,
                'scrollInput' => false,
            ]
        );
        $view->registerJs("$('#{$this->options['id']}').datetimepicker({$params});", View::POS_READY);
    }
}