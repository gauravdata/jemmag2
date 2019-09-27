<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Source\Filter;

use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CategoryListStyles
 * @package Aheadworks\Layerednav\Model\Source\Filter
 */
class CategoryListStyles implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => FilterCategoryInterface::CATEGORY_STYLE_DEFAULT,
                'label' => __('Default (multiselect)')
            ],
            [
                'value' => FilterCategoryInterface::CATEGORY_STYLE_SINGLE_PATH,
                'label' => __('Single path')
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
