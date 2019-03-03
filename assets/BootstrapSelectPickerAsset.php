<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

class BootstrapSelectPickerAsset extends AssetBundle
{
    public $sourcePath = '@app/node_modules/bootstrap-select/dist';
    public $css = [
        'css/bootstrap-select.min.css'
    ];
    public $js = [
        'js/bootstrap-select.min.js',
        'js/i18n/defaults-ru_RU.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
    
}
