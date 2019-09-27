<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Image;

use Aheadworks\Layerednav\Model\File\Info as FileInfo;
use Aheadworks\Layerednav\Api\Data\ImageInterface;
use Aheadworks\Layerednav\Model\Image\ViewInterface as ImageViewInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Resolver
 *
 * @package Aheadworks\Layerednav\Model\Image
 */
class Resolver
{
    /**
     * @var FileInfo
     */
    private $fileInfo;

    /**
     * @param FileInfo $fileInfo
     */
    public function __construct(
        FileInfo $fileInfo
    ) {
        $this->fileInfo = $fileInfo;
    }

    /**
     * Retrieve image view data
     *
     * @param array $imageData
     * @return array
     * @throws FileSystemException
     * @throws NoSuchEntityException
     */
    public function getViewData($imageData)
    {
        $viewData = [];

        $fileName = isset($imageData[ImageInterface::FILE_NAME]) ? $imageData[ImageInterface::FILE_NAME] : '';

        $viewData[ImageViewInterface::ID] =
            isset($imageData[ImageInterface::ID])
                ? $imageData[ImageInterface::ID]
                : null;
        $viewData[ImageViewInterface::URL] = $this->fileInfo->getMediaUrl($fileName);
        $viewData[ImageViewInterface::TYPE] = $this->fileInfo->getMimeType($fileName);
        $viewData[ImageViewInterface::TITLE] = $fileName;
        $viewData[ImageViewInterface::NAME] =
            isset($imageData[ImageInterface::NAME])
                ? $imageData[ImageInterface::NAME]
                : '';
        $viewData[ImageViewInterface::FILE_NAME] = $fileName;

        $statisticsData = $this->fileInfo->getStatisticsData($fileName);
        $viewData[ImageViewInterface::SIZE] = isset($statisticsData['size']) ? $statisticsData['size'] : 0;

        return $viewData;
    }
}
