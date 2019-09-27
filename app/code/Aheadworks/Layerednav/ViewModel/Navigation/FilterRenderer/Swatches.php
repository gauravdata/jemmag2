<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer;

use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Layer\Filter\Checker as FilterChecker;
use Magento\Eav\Model\Entity\Attribute\Option as AttributeOption;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\Checker as FilterItemChecker;
use Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches\Resolver as SwatchesResolver;
use Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches\Processor as SwatchesProcessor;

/**
 * Class Swatches
 *
 * @package Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer
 */
class Swatches extends Base
{
    /**
     * @var SwatchesResolver
     */
    private $swatchesResolver;

    /**
     * @var SwatchesProcessor
     */
    private $swatchesProcessor;

    /**
     * @var FilterChecker
     */
    private $filterChecker;

    /**
     * @param Config $config
     * @param FilterItemChecker $filterItemChecker
     * @param SwatchesResolver $swatchesResolver
     * @param SwatchesProcessor $swatchesProcessor
     * @param FilterChecker $filterChecker
     */
    public function __construct(
        Config $config,
        FilterItemChecker $filterItemChecker,
        SwatchesResolver $swatchesResolver,
        SwatchesProcessor $swatchesProcessor,
        FilterChecker $filterChecker
    ) {
        parent::__construct($config, $filterItemChecker);
        $this->swatchesResolver = $swatchesResolver;
        $this->swatchesProcessor = $swatchesProcessor;
        $this->filterChecker = $filterChecker;
    }

    /**
     * Check if multi select available
     *
     * @param FilterInterface $filter
     * @return bool
     */
    public function isMultiselectAvailable(FilterInterface $filter)
    {
        return $this->filterChecker->isMultiselectAvailable($filter);
    }

    /**
     * Retrieve array with swatches data
     *
     * @param FilterInterface $filter
     * @return array
     */
    public function getSwatchesData($filter)
    {
        $optionsData = [];

        $attributeModel = $filter->getAttributeModel();
        /** @var AttributeOption $attributeOption */
        foreach ($attributeModel->getOptions() as $attributeOption) {
            $swatchOptionData = [];
            $attributeOptionValue = $this->swatchesResolver->getOptionValue($attributeOption);
            $attributeOptionFilterItem = $this->getFilterItemByValue(
                $filter->getItems(),
                $attributeOptionValue
            );
            if ($attributeOptionFilterItem
                && ($this->isNeedToShowItem($attributeOptionFilterItem)
                    || ($this->swatchesResolver->isNeedToShowEmptyResults($attributeModel)))
            ) {
                $swatchOptionData = [
                    'label' => $attributeOption->getLabel(),
                    'classes' => [
                        'disabled' => ($attributeOptionFilterItem->getCount() < 1),
                        'active' => $this->isActiveItem($attributeOptionFilterItem),
                    ],
                    'value' => $attributeOption->getValue(),
                ];
            } elseif ($this->swatchesResolver->isNeedToShowEmptyResults($attributeModel)) {
                $swatchOptionData = [
                    'label' => $attributeOption->getLabel(),
                    'classes' => [
                        'disabled' => true,
                        'active' => false,
                    ],
                    'value' => $attributeOption->getValue(),
                ];
            }
            if (!empty($swatchOptionData)) {
                $optionsData[$attributeOptionValue] = $swatchOptionData;
            }
        }
        $optionsData = $this->swatchesProcessor->addOptionsSwatchesData($optionsData);

        return [
            'attribute_id' => $attributeModel->getId(),
            'attribute_code' => $attributeModel->getAttributeCode(),
            'attribute_label' => $attributeModel->getStoreLabel(),
            'options_data' => $optionsData,
        ];
    }

    /**
     * Get filter item by value
     *
     * @param FilterItemInterface[] $filterItems
     * @param string $value
     * @return bool|FilterItemInterface
     */
    private function getFilterItemByValue($filterItems, $value)
    {
        foreach ($filterItems as $item) {
            if ($item->getValue() == $value) {
                return $item;
            }
        }
        return false;
    }

    /**
     * Retrieve attribute code from swatches data array
     *
     * @param array $swatchesData
     * @return string
     */
    public function getAttributeCode($swatchesData)
    {
        return isset($swatchesData['attribute_code']) ? $swatchesData['attribute_code'] : '';
    }

    /**
     * Retrieve attribute code from swatches data array
     *
     * @param array $swatchesData
     * @return string
     */
    public function getAttributeId($swatchesData)
    {
        return isset($swatchesData['attribute_id']) ? $swatchesData['attribute_id'] : '';
    }

    /**
     * Retrieve attribute options from swatches data array
     *
     * @param array $swatchesData
     * @return array
     */
    public function getOptions($swatchesData)
    {
        return isset($swatchesData['options_data']) ? $swatchesData['options_data'] : [];
    }

    /**
     * Retrieve option label from data array
     *
     * @param array $optionViewData
     * @return string
     */
    public function getOptionLabel($optionViewData)
    {
        return isset($optionViewData['label']) ? $optionViewData['label'] : '';
    }

    /**
     * Retrieve option classes from data array
     *
     * @param array $optionViewData
     * @return string
     */
    public function getOptionClasses($optionViewData)
    {
        $classes = '';
        $classesData = isset($optionViewData['classes']) ? $optionViewData['classes'] : [];
        foreach ($classesData as $className => $flag) {
            if ($flag) {
                $classes .= $className . ' ';
            }
        }
        return $classes;
    }

    /**
     * Retrieve option swatches type from data array
     *
     * @param array $optionViewData
     * @return string
     */
    public function getOptionSwatchesType($optionViewData)
    {
        return isset($optionViewData['swatches_type']) ? $optionViewData['swatches_type'] : '';
    }

    /**
     * Retrieve option swatches id from data array
     *
     * @param array $optionViewData
     * @return string
     */
    public function getOptionSwatchesId($optionViewData)
    {
        return isset($optionViewData['swatches_option_id']) ? $optionViewData['swatches_option_id'] : '';
    }

    /**
     * Retrieve option swatches tooltip thumb from data array
     *
     * @param array $optionViewData
     * @return string
     */
    public function getOptionSwatchesTooltipThumb($optionViewData)
    {
        return isset($optionViewData['swatches_tooltip_thumb']) ? $optionViewData['swatches_tooltip_thumb'] : '';
    }

    /**
     * Retrieve option swatches tooltip value from data array
     *
     * @param array $optionViewData
     * @return string
     */
    public function getOptionSwatchesTooltipValue($optionViewData)
    {
        return isset($optionViewData['swatches_tooltip_value']) ? $optionViewData['swatches_tooltip_value'] : '';
    }

    /**
     * Retrieve option custom style from data array
     *
     * @param array $optionViewData
     * @return string
     */
    public function getOptionCustomStyle($optionViewData)
    {
        return isset($optionViewData['custom_style']) ? $optionViewData['custom_style'] : '';
    }

    /**
     * Retrieve option swatches value from data array
     *
     * @param array $optionViewData
     * @return string
     */
    public function getOptionSwatchesValue($optionViewData)
    {
        return isset($optionViewData['swatches_value']) ? $optionViewData['swatches_value'] : '';
    }

    /**
     * Check if need to display value for swatches option
     *
     * @param string $optionSwatchesType
     * @return bool
     */
    public function isNeedToDisplayOptionSwatchesValue($optionSwatchesType)
    {
        return $this->swatchesResolver->isNeedToDisplayOptionSwatchesValue($optionSwatchesType);
    }
}
