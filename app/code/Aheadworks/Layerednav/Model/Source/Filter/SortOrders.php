<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Source\Filter;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class SortOrders
 * @package Aheadworks\Layerednav\Model\Source\Filter
 */
class SortOrders implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => FilterInterface::SORT_ORDER_MANUAL,
                'label' => __('Manually (the order is set manually in the attribute settings)')
            ],
            [
                'value' => FilterInterface::SORT_ORDER_ASC,
                'label' => __('A-Z')
            ],
            [
                'value' => FilterInterface::SORT_ORDER_DESC,
                'label' => __('Z-A')
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
