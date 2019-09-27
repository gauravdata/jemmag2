<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches;

use Magento\Swatches\Helper\Data as SwatchesDataHelper;
use Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches\DataResolver\Pool as DataResolverPool;

/**
 * Class DataProvider
 *
 * @package Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches
 */
class DataProvider
{
    /**
     * @var SwatchesDataHelper
     */
    private $dataHelper;

    /**
     * @var DataResolverPool
     */
    private $dataResolverPool;

    /**
     * @param SwatchesDataHelper $dataHelper
     * @param DataResolverPool $dataResolverPool
     */
    public function __construct(
        SwatchesDataHelper $dataHelper,
        DataResolverPool $dataResolverPool
    ) {
        $this->dataHelper = $dataHelper;
        $this->dataResolverPool = $dataResolverPool;
    }

    /**
     * Retrieve prepared array of swatches data for specified options
     *
     * @param array $optionIds
     * @return array
     */
    public function getPreparedSwatchesData($optionIds)
    {
        $swatchesData = $this->dataHelper->getSwatchesByOptionsId($optionIds);
        $preparedData = [];
        foreach ($swatchesData as $key => $dataRow) {
            $preparedDataRow = [];
            if (isset($dataRow['type'])) {
                $swatchesType = $dataRow['type'];
                $dataResolver = $this->dataResolverPool->getResolverBySwatchesType($swatchesType);
                if ($dataResolver) {
                    $preparedDataRow['swatches_type'] = isset($dataRow['type']) ? $dataRow['type'] : '';
                    $preparedDataRow['swatches_value'] = isset($dataRow['value']) ? $dataRow['value'] : '';
                    $preparedDataRow['swatches_option_id'] = isset($dataRow['option_id']) ? $dataRow['option_id'] : '';
                    $preparedDataRow['classes'] = $dataResolver->getClasses();
                    $preparedDataRow['swatches_tooltip_thumb'] = $dataResolver->getTooltipThumb($dataRow);
                    $preparedDataRow['swatches_tooltip_value'] = $dataResolver->getTooltipValue($dataRow);
                    $preparedDataRow['custom_style'] = $dataResolver->getCustomStyle($dataRow);
                }
            }
            $preparedData[$key] = $preparedDataRow;
        }
        return $preparedData;
    }
}
