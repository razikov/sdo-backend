<?php

namespace app\assets;

use yii\web\AssetBundle;

class CKEditorAsset extends AssetBundle
{
    public $sourcePath = '@app/node_modules/@ckeditor/ckeditor5-build-classic/build';
    public $js = [
        'ckeditor.js',
    ];
}
