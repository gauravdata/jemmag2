<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Config\Source;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class FilterDisplayState
 * @package Aheadworks\Layerednav\Model\Config\Source
 */
class FilterDisplayState implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => FilterInterface::DISPLAY_STATE_EXPANDED,
                'label' => __('Expanded')
            ],
            [
                'value' => FilterInterface::DISPLAY_STATE_COLLAPSED,
                'label' => __('Collapsed')
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
