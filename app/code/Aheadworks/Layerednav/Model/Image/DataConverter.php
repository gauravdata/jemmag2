<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Image;

use Aheadworks\Layerednav\Api\Data\ImageInterface;
use Aheadworks\Layerednav\Api\Data\ImageInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class DataConverter
 *
 * @package Aheadworks\Layerednav\Model\Image
 */
class DataConverter
{
    /**
     * @var ImageInterfaceFactory
     */
    private $imageFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param ImageInterfaceFactory $imageFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        ImageInterfaceFactory $imageFactory,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->imageFactory = $imageFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Convert data array to model
     *
     * @param array $data
     * @return ImageInterface
     */
    public function dataArrayToModel($data)
    {
        /** @var ImageInterface $image */
        $image = $this->imageFactory->create();
        $this->dataObjectHelper->populateWithArray($image, $data, ImageInterface::class);

        return $image;
    }

    /**
     * Convert model to data array
     *
     * @param ImageInterface $image
     * @return array
     */
    public function modelToDataArray($image)
    {
        $data = $this->dataObjectProcessor->buildOutputDataArray($image, ImageInterface::class);

        return $data;
    }
}
