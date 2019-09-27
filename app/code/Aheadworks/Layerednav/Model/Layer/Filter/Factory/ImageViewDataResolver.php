<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Factory;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Image\Resolver as ImageResolver;
use Aheadworks\Layerednav\Model\Image\ViewInterface as ImageViewInterface;
use Aheadworks\Layerednav\Model\Image\DataConverter as ImageDataConverter;
use Aheadworks\Layerednav\Model\Image\View\DataConverter as ImageViewDataConverter;

/**
 * Class ImageViewDataResolver
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Factory
 */
class ImageViewDataResolver
{
    /**
     * @var ImageResolver
     */
    private $imageResolver;

    /**
     * @var ImageDataConverter
     */
    private $imageDataConverter;

    /**
     * @var ImageViewDataConverter
     */
    private $imageViewDataConverter;

    /**
     * @param ImageResolver $imageResolver
     * @param ImageDataConverter $imageDataConverter
     * @param ImageViewDataConverter $imageViewDataConverter
     */
    public function __construct(
        ImageResolver $imageResolver,
        ImageDataConverter $imageDataConverter,
        ImageViewDataConverter $imageViewDataConverter
    ) {
        $this->imageResolver = $imageResolver;
        $this->imageDataConverter = $imageDataConverter;
        $this->imageViewDataConverter = $imageViewDataConverter;
    }

    /**
     * Get image view data
     *
     * @param FilterInterface $filterObject
     * @return ImageViewInterface|null
     * @throws \Exception
     */
    public function getImageView($filterObject)
    {
        $image = $filterObject->getImage();
        if ($image && $image->getFileName()) {
            $imageData = $this->imageDataConverter->modelToDataArray($image);
            $imageViewData = $this->imageResolver->getViewData($imageData);
            $imageView = $this->imageViewDataConverter->dataArrayToModel($imageViewData);
            return $imageView;
        }
        return null;
    }
}
