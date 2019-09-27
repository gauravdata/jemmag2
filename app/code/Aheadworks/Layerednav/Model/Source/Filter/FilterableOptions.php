<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Source\Filter;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\LayeredNavigation\Model\Attribute\Source\FilterableOptions as FilterableOptionsSource;

/**
 * Class FilterableOptions
 * @package Aheadworks\Layerednav\Model\Source\Filter
 */
class FilterableOptions implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var FilterableOptionsSource
     */
    private $filterableOptionsSource;

    /**
     * @param FilterableOptionsSource $filterableOptionsSource
     */
    public function __construct(
        FilterableOptionsSource $filterableOptionsSource
    ) {
        $this->filterableOptionsSource = $filterableOptionsSource;
    }
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = $this->filterableOptionsSource->toOptionArray();
        }
        return $this->options;
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
