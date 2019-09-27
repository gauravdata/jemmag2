<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Model\Filter\PostDataProcessorInterface;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Aheadworks\Layerednav\Api\Data\ImageInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;

/**
 * Class Swatches
 *
 * @package Aheadworks\Layerednav\Model\Filter\PostDataProcessor
 */
class Swatches implements PostDataProcessorInterface
{
    /**
     * key for swathes data in the post data array
     */
    const SWATCHES_DATA_KEY = 'swatches';

    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        $swatchesData = isset($data[self::SWATCHES_DATA_KEY]) ? $data[self::SWATCHES_DATA_KEY] : [];

        foreach ($swatchesData as &$swatchItem) {
            if (is_array($swatchItem)) {
                $swatchItem = $this->getProcessedImageData($swatchItem);
                $swatchItem = $this->getProcessedStorefrontTitles($swatchItem);
            }
        }

        $data['extension_attributes']['swatches'] = $swatchesData;
        return $data;
    }

    /**
     * Retrieve prepared swatch item image data
     *
     * @param array $swatchItem
     * @return array
     */
    private function getProcessedImageData($swatchItem)
    {
        if (isset($swatchItem['swatch'])) {
            if ($this->isColorSelected($swatchItem['swatch'])) {
                $swatchItem[SwatchInterface::VALUE] = $swatchItem['swatch'];
                $swatchItem[SwatchInterface::IMAGE] = null;
            } else {
                $swatchItem[SwatchInterface::VALUE] = null;
                $swatchItem[SwatchInterface::IMAGE] = [
                    ImageInterface::FILE_NAME => $swatchItem['swatch']
                ];
            }
        }
        return $swatchItem;
    }

    /**
     * Check if swatch value describes color
     *
     * @param string $swatchValue
     * @return bool
     */
    private function isColorSelected($swatchValue)
    {
        return (strpos($swatchValue, '#') == 0
            && strpos($swatchValue, '.') === false);
    }

    /**
     * Retrieve prepared swatch item storefront titles data
     *
     * @param array $swatchItem
     * @return array
     */
    private function getProcessedStorefrontTitles($swatchItem)
    {
        if (isset($swatchItem[SwatchInterface::STOREFRONT_TITLES])
            && is_array($swatchItem[SwatchInterface::STOREFRONT_TITLES])
        ) {
            $titlesData = $swatchItem[SwatchInterface::STOREFRONT_TITLES];
            $preparedTitlesData = [];
            foreach ($titlesData as $storeId => $value) {
                $preparedTitlesData[] = [
                    StoreValueInterface::STORE_ID => $storeId,
                    StoreValueInterface::VALUE => $value,
                ];
            }
            $swatchItem[SwatchInterface::STOREFRONT_TITLES] = $preparedTitlesData;
        }
        return $swatchItem;
    }
}
