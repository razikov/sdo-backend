<?php

namespace app\widgets;

use yii\helpers\Html;
use yii\widgets\InputWidget;
use app\assets\CKEditorAsset;
use app\assets\AxiosAsset;

class CKEditor extends InputWidget
{

    public function run()
    {
        $view = $this->getView();
        CKEditorAsset::register($view);
        AxiosAsset::register($view);
        $id = isset($this->options['id']) ? $this->options['id'] : $this->getId();
        
        if ($this->hasModel()) {
            echo Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textarea($this->name, $this->value, $this->options);
        }
        $view->registerJsFile('/js/UploadAdapter.js');
        $view->registerJs("var editor = ClassicEditor
            .create( document.querySelector( '#{$id}' ) )
            .then( editor => {
                editor.plugins.get( 'FileRepository' ).createUploadAdapter = function( loader ) {
                    return new UploadAdapter( loader );
                };
            } )
            .catch( error => {
                console.error( error );
            } );");
    }
}