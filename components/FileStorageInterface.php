<?php

namespace app\components;

use app\models\Upload;

interface FileStorageInterface
{
    /**
     * @param string $filePath
     * @param string $fileName
     * @param string $mime
     * @return Upload
     */
    public function upload($filePath, $fileName, $mime);

    /**
     * @param Upload $upload
     * @return string
     */
    public function getUrl(Upload $upload);
}
