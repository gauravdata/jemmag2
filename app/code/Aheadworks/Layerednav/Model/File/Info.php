<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\File;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\File\Mime;
use Magento\Store\Model\Store;

/**
 * Class Info
 *
 * @package Aheadworks\Layerednav\Model\File
 */
class Info
{
    /**
     * @var string
     */
    const FILE_DIR = 'aw_ln/media';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var Mime
     */
    private $fileMime;

    /**
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     * @param Mime $fileMime
     */
    public function __construct(
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        Mime $fileMime
    ) {
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->fileMime = $fileMime;
    }

    /**
     * Retrieves base media url for folder
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseMediaUrl()
    {
        /** @var Store $store */
        $store = $this->storeManager->getStore();
        $storeBaseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        return $storeBaseUrl . self::FILE_DIR . '/';
    }

    /**
     * Get file url
     *
     * @param string $file
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl($file)
    {
        $file = ltrim(str_replace('\\', '/', $file), '/');

        $baseMediaUrl = $this->getBaseMediaUrl();

        return $baseMediaUrl . $file;
    }

    /**
     * Get file statistics data
     *
     * @param string $fileName
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getStatisticsData($fileName)
    {
        return $this->getMediaDirectory()->stat($this->getFilePath($fileName));
    }

    /**
     * Get file mime-type
     *
     * @param string $fileName
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getMimeType($fileName)
    {
        return $this->fileMime->getMimeType($this->getFullPath($fileName));
    }

    /**
     * Get WriteInterface instance
     *
     * @return WriteInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }
        return $this->mediaDirectory;
    }

    /**
     * Get file path
     *
     * @param string $fileName
     * @return string
     */
    private function getFilePath($fileName)
    {
        $DS = DIRECTORY_SEPARATOR;
        return self::FILE_DIR . $DS . ltrim($fileName, '/');
    }

    /**
     * Get full file path
     *
     * @param string $fileName
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getFullPath($fileName)
    {
        return $this->getMediaDirectory()->getAbsolutePath($this->getFilePath($fileName));
    }
}
