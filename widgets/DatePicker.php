<?php

namespace app\widgets;

use yii\helpers\FormatConverter;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\InputWidget;
use app\assets\JqueryDatePickerAsset;

class DatePicker extends InputWidget
{
    public $format;
    public $language;
    public $params = [];

    public function init()
    {
        parent::init();
        if ($this->format === null) {
            $this->format = FormatConverter::convertDateIcuToPhp(\Yii::$app->formatter->dateFormat);
        }
        if ($this->language === null) {
            $this->language = substr(\Yii::$app->language, 0, 2);
        }
        Html::addCssClass($this->options, 'datepicker');
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
            array_merge(
                [
                    'format' => $this->format,
                    'lang' => $this->language,
                    'dayOfWeekStart' => 1,
                    'timepicker' => false,
                ],
                $this->params
            )
        );
        $view->registerJs("$('#{$this->options['id']}').datetimepicker({$params});", View::POS_READY);
    }
}