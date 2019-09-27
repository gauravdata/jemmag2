<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Source\Filter;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class FilterTypes
 * @package Aheadworks\Layerednav\Model\Source\Filter
 */
class Types implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => FilterInterface::CATEGORY_FILTER,
                'label' => __('Category')
            ],
            [
                'value' => FilterInterface::ATTRIBUTE_FILTER,
                'label' => __('Attribute')
            ],
            [
                'value' => FilterInterface::PRICE_FILTER,
                'label' => __('Price')
            ],
            [
                'value' => FilterInterface::DECIMAL_FILTER,
                'label' => __('Numeric')
            ],
            [
                'value' => FilterInterface::SALES_FILTER,
                'label' => __('On Sale')
            ],
            [
                'value' => FilterInterface::NEW_FILTER,
                'label' => __('New')
            ],
            [
                'value' => FilterInterface::STOCK_FILTER,
                'label' => __('In Stock')
            ],
        ];
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        $optionsArray = $this->toOptionArray();
        $options = [];
        foreach ($optionsArray as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    /**
     * Get option by value
     *
     * @param int $value
     * @return string|null
     */
    public function getOptionByValue($value)
    {
        $options = $this->getOptions();
        if (array_key_exists($value, $options)) {
            return $options[$value];
        }
        return null;
    }
}
