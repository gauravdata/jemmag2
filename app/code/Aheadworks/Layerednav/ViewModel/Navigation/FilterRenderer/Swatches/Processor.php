<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches;

use Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches\DataProvider as SwatchesDataProvider;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class Processor
 *
 * @package Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches
 */
class Processor
{
    /**
     * @var SwatchesDataProvider
     */
    private $swatchesDataProvider;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param SwatchesDataProvider $swatchesDataProvider
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        SwatchesDataProvider $swatchesDataProvider,
        ArrayManager $arrayManager
    ) {
        $this->swatchesDataProvider = $swatchesDataProvider;
        $this->arrayManager = $arrayManager;
    }

    /**
     * Add swatches data to options array
     *
     * @param array $optionsData
     * @return array
     */
    public function addOptionsSwatchesData($optionsData)
    {
        $preparedOptionsData = $optionsData;
        $optionIds = [];
        foreach ($optionsData as $optionDataRow) {
            if (isset($optionDataRow['value'])) {
                $optionIds[] = $optionDataRow['value'];
            }
        }

        $swatchesData = $this->swatchesDataProvider->getPreparedSwatchesData($optionIds);

        foreach ($optionsData as $optionKey => $optionDataRow) {
            if (isset($optionDataRow['value'])
                && isset($swatchesData[$optionDataRow['value']])
                && is_array($swatchesData[$optionDataRow['value']])
            ) {
                $preparedOptionsData = $this->arrayManager->merge(
                    $optionKey,
                    $preparedOptionsData,
                    $swatchesData[$optionDataRow['value']]
                );
            }
        }
        return $preparedOptionsData;
    }
}
