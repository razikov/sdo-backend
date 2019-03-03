<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

class JqueryDatePickerAsset extends AssetBundle
{
    public $sourcePath = '@app/node_modules/jquery-datetimepicker/build';
    public $css = ['jquery.datetimepicker.min.css'];
    public $js = ['jquery.datetimepicker.full.min.js'];
    public $depends = ['yii\web\JqueryAsset'];

    public static function register($view)
    {
        parent::register($view);
        $locale = substr(\Yii::$app->language, 0, 2);
        $view->registerJs("jQuery.datetimepicker.setLocale(\"{$locale}\");", View::POS_END);
    }
}
