<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Source\Filter;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CategoryModes
 * @package Aheadworks\Layerednav\Model\Source\Filter
 */
class CategoryModes implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => FilterInterface::CATEGORY_MODE_ALL,
                'label' => __('Everywhere, where applicable')
            ],
            [
                'value' => FilterInterface::CATEGORY_MODE_LOWEST_LEVEL,
                'label' => __('Only in categories of the lowest level')
            ],
            [
                'value' => FilterInterface::CATEGORY_MODE_EXCLUDE,
                'label' => __('Exclude specific categories')
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
