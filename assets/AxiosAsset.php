<?php

namespace app\assets;

use yii\web\AssetBundle;

class AxiosAsset extends AssetBundle
{
    public $sourcePath = '@app/node_modules/axios/dist';
    public $js = [
        'axios.min.js',
    ];
}
