<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Model\Filter\PostDataProcessorInterface;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;

/**
 * Class NativeSwatches
 *
 * @package Aheadworks\Layerednav\Model\Filter\PostDataProcessor
 */
class NativeSwatches implements PostDataProcessorInterface
{
    /**
     * key for swathes data in the post data array
     */
    const SWATCHES_DATA_KEY = 'native_visual_swatches';

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

        $data['extension_attributes']['native_visual_swatches'] = $swatchesData;
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
            $swatchItem[SwatchInterface::VALUE] = $swatchItem['swatch'];
        }
        return $swatchItem;
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
