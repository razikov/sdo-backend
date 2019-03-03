<?php

namespace app\widgets;

use yii\helpers\Html;
use yii\widgets\InputWidget;
use app\assets\BootstrapSelectPickerAsset;

class Select extends InputWidget
{
    public $items;

    public function init()
    {
        parent::init();
        Html::addCssClass($this->options, 'selectpicker');
    }

    public function run()
    {
        if ($this->hasModel()) {
            echo Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->options);
        } else {
            echo Html::dropDownList($this->name, $this->value, $this->items, $this->options);
        }
        $view = $this->getView();
        BootstrapSelectPickerAsset::register($view);
        $view->registerJs("$('#{$this->options['id']}').selectpicker();", \yii\web\View::POS_READY);
    }
}