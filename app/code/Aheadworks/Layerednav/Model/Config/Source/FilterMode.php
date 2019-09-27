<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Config\Source;

use Aheadworks\Layerednav\Api\Data\Filter\ModeInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class FilterMode
 * @package Aheadworks\Layerednav\Model\Config\Source
 */
class FilterMode implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => ModeInterface::MODE_SINGLE_SELECT,
                'label' => __('Single Select')
            ],
            [
                'value' => ModeInterface::MODE_MULTI_SELECT,
                'label' => __('Multiple Select')
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
