<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer;

use Aheadworks\Layerednav\Model\Filter\Swatch\Finder as SwatchFinder;
use Aheadworks\Layerednav\Model\Image\DataConverter as ImageDataConverter;
use Aheadworks\Layerednav\Model\Image\Resolver as ImageResolver;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Swatch
 *
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer
 */
class Swatch
{
    /**
     * @var SwatchFinder
     */
    private $swatchFinder;

    /**
     * @var ImageDataConverter
     */
    private $imageDataConverter;

    /**
     * @var ImageResolver
     */
    private $imageResolver;

    /**
     * @param SwatchFinder $swatchFinder
     * @param ImageDataConverter $imageDataConverter
     * @param ImageResolver $imageResolver
     */
    public function __construct(
        SwatchFinder $swatchFinder,
        ImageDataConverter $imageDataConverter,
        ImageResolver $imageResolver
    ) {
        $this->swatchFinder = $swatchFinder;
        $this->imageDataConverter = $imageDataConverter;
        $this->imageResolver = $imageResolver;
    }

    /**
     * Process options to add swatch image data
     *
     * @param array $options [['value' => ..., 'label' => ...], ...]
     * @return array
     */
    public function perform($options)
    {
        $result = [];
        foreach ($options as $option) {
            if (is_array($option['value']) || empty($option['value'])) {
                continue;
            }
            $data = $option;

            $data['image'] = $this->getSwatchImageViewData($option['value']);

            $result[] = $data;
        }

        return $result;
    }

    /**
     * Retrieve array of swatch image view data for specific option
     *
     * @param int $optionId
     * @return array
     */
    protected function getSwatchImageViewData($optionId)
    {
        $swatchImageViewData = [];
        try {
            $swatchItem = $this->swatchFinder->getByOptionId($optionId);
            if ($swatchItem) {
                $image = $swatchItem->getImage();
                if ($image) {
                    $imageData = $this->imageDataConverter->modelToDataArray($image);
                    $swatchImageViewData = $this->imageResolver->getViewData($imageData);
                } elseif (!empty($swatchItem->getValue())) {
                    $swatchImageViewData = [
                        'color' => $swatchItem->getValue(),
                    ];
                }
            }
        } catch (LocalizedException $exception) {
            $swatchImageViewData = [];
        }

        return $swatchImageViewData;
    }
}
