<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\Exception;
use app\models\Upload;

/**
 * @property string $basePath
 * @property string $baseUrl
 */
class LocalStorage extends Component implements FileStorageInterface
{
    public $basePath;

    public $baseUrl;

    /**
     * @param string $filePath
     * @param string $fileName
     * @param string $mime
     * @return string
     */
    public function upload($filePath, $fileName, $mime)
    {
        $destinationPath = Yii::getAlias($this->basePath.'/'.$fileName);
        copy($filePath, $destinationPath);

        return $fileName;
    }

    /**
     * @param Upload $upload
     * @return string
     * @throws Exception
     */
    public function getUrl(Upload $upload)
    {
        if ($upload->type != Upload::TYPE_LOCAL) {
            throw new Exception("Неизвестный тип хранилища {$upload->type}");
        }

        return $this->baseUrl.'/'.$upload->filename;
    }
}
