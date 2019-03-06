<?php

namespace app\components;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;

/**
 * Контейнер компонентов хранилищ
 *
 * @property array $storages
 */
class StorageContainer extends Component
{
    /** @var array */
    public $storages;

    /** @var FileStorageInterface[] */
    private $storageInstances = [];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!is_array($this->storages) || empty($this->storages)) {
            throw new InvalidConfigException('Не задан массив storages');
        }

        foreach ($this->storages as $storageType => $storageConfig) {
            $this->storageInstances[$storageType] = Instance::ensure($storageConfig, FileStorageInterface::class);
        }
    }

    /**
     * @param string $storageType
     * @return FileStorageInterface
     * @throws InvalidConfigException
     */
    public function getFileStorageByUploadType($storageType)
    {
        if (!isset($this->storageInstances[$storageType])) {
            throw new InvalidConfigException('Отсутствует данный тип storage');
        }

        return $this->storageInstances[$storageType];
    }
}