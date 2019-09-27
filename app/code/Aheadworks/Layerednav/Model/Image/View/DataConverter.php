<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Image\View;

use Aheadworks\Layerednav\Model\Image\ViewInterface as ImageViewInterface;
use Aheadworks\Layerednav\Model\Image\ViewInterfaceFactory as ImageViewInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class DataConverter
 *
 * @package Aheadworks\Layerednav\Model\Image\View
 */
class DataConverter
{
    /**
     * @var ImageViewInterfaceFactory
     */
    private $imageViewFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param ImageViewInterfaceFactory $imageViewFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        ImageViewInterfaceFactory $imageViewFactory,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->imageViewFactory = $imageViewFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Convert data array to model
     *
     * @param array $data
     * @return ImageViewInterface
     */
    public function dataArrayToModel($data)
    {
        /** @var ImageViewInterface $image */
        $imageView = $this->imageViewFactory->create();
        $this->dataObjectHelper->populateWithArray($imageView, $data, ImageViewInterface::class);

        return $imageView;
    }

    /**
     * Convert model to data array
     *
     * @param ImageViewInterface $imageView
     * @return array
     */
    public function modelToDataArray($imageView)
    {
        $data = $this->dataObjectProcessor->buildOutputDataArray($imageView, ImageViewInterface::class);

        return $data;
    }
}
