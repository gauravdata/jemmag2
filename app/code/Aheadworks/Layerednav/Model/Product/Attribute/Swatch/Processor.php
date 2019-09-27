<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Product\Attribute\Swatch;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Aheadworks\Layerednav\Api\Data\FilterExtensionInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Filter\Swatch\Converter as FilterSwatchConverter;
use Aheadworks\Layerednav\Model\Product\Attribute\Option\Resolver as AttributeOptionResolver;
use Magento\Eav\Model\Entity\Attribute\Option;
use Magento\Eav\Api\Data\AttributeOptionLabelInterface;

/**
 * Class Processor
 *
 * @package Aheadworks\Layerednav\Model\Product\Attribute\Swatch
 */
class Processor
{
    /**
     * @var FilterSwatchConverter
     */
    private $filterSwatchConverter;

    /**
     * @var AttributeOptionResolver
     */
    private $attributeOptionResolver;

    /**
     * @param FilterSwatchConverter $filterSwatchConverter
     * @param AttributeOptionResolver $attributeOptionResolver
     */
    public function __construct(
        FilterSwatchConverter $filterSwatchConverter,
        AttributeOptionResolver $attributeOptionResolver
    ) {
        $this->filterSwatchConverter = $filterSwatchConverter;
        $this->attributeOptionResolver = $attributeOptionResolver;
    }

    /**
     * Set attribute options by filter swatches
     *
     * @param AbstractAttribute $attribute
     * @param FilterInterface $filter
     * @return AbstractAttribute
     */
    public function setOptionsByFilter($attribute, $filter)
    {
        $newOptions = $this->getNewOptionsFromFilter($filter);

        $existingOptions = $this->attributeOptionResolver->getByAttribute($attribute);

        $optionsIdsToDelete = $this->getOptionIdsToDelete($newOptions, $existingOptions);

        $optionsData = $this->getOptionsData($newOptions, $optionsIdsToDelete);
        $attribute->setData('optionvisual', $optionsData);

        $defaultOptionId = $this->getDefaultOptionId($newOptions);
        if ($defaultOptionId) {
            $default[] = $defaultOptionId;
            $attribute->setData('defaultvisual', $default);
        }

        $swatchesData = $this->getSwatchesData($newOptions);
        $attribute->setData('swatchvisual', $swatchesData);

        return $attribute;
    }

    /**
     * Retrieve filter swatches, converted to the product attribute options array
     *
     * @param FilterInterface $filter
     * @return Option[]
     */
    protected function getNewOptionsFromFilter($filter)
    {
        $newOptions = [];

        /** @var FilterExtensionInterface $filterExtensionAttributes */
        $filterExtensionAttributes = $filter->getExtensionAttributes();
        if ($filterExtensionAttributes->getNativeVisualSwatches()) {
            /** @var SwatchInterface[] $swatches */
            $swatches = $filterExtensionAttributes->getNativeVisualSwatches();
            foreach ($swatches as $swatchItem) {
                $option = $this->filterSwatchConverter->toSwatchAttributeOption($swatchItem);
                $newOptions[] = $option;
            }
        }

        return $newOptions;
    }

    /**
     * Get options data array in the specific format, required for correct saving
     *
     * @param Option[] $options
     * @param array $optionsIdsToDelete
     * @return mixed
     */
    protected function getOptionsData($options, $optionsIdsToDelete)
    {
        $optionsData = [];
        $optionIndex = 0;
        $sortOrder = 0;
        foreach ($options as $optionItem) {
            $optionIndex++;
            $optionId = $optionItem->getId();
            $optionsData['value'][$optionId][0] = $optionItem->getLabel();
            $optionsData['order'][$optionId] = $optionItem->getSortOrder() ?: $sortOrder++;
            if (is_array($optionItem->getStoreLabels())) {
                /** @var AttributeOptionLabelInterface $label */
                foreach ($optionItem->getStoreLabels() as $label) {
                    $optionsData['value'][$optionId][$label->getStoreId()] = $label->getLabel();
                }
            }
        }

        foreach ($optionsIdsToDelete as $optionId) {
            $optionsData['delete'][$optionId] = '1';
            if (!isset($optionsData['value'][$optionId])) {
                $optionsData['value'][$optionId] = [];
            }
        }

        return $optionsData;
    }

    /**
     * Compare new and existing options arrays to retrieve ids of options to delete
     *
     * @param Option[] $newOptions
     * @param Option[] $existingOptions
     * @return array
     */
    protected function getOptionIdsToDelete($newOptions, $existingOptions)
    {
        $newOptionsIds = $this->getOptionsIds($newOptions);
        $existingOptionsIds = $this->getOptionsIds($existingOptions);

        $optionIdsToDelete = array_diff($existingOptionsIds, $newOptionsIds);

        return $optionIdsToDelete;
    }

    /**
     * Retrieve array with options ids only
     *
     * @param Option[] $options
     * @return array
     */
    protected function getOptionsIds($options)
    {
        $ids = [];
        foreach ($options as $optionItem) {
            $ids[] = $optionItem->getId();
        }
        return $ids;
    }

    /**
     * Retrieve default option id
     *
     * @param Option[] $options
     * @return int|null
     */
    protected function getDefaultOptionId($options)
    {
        $defaultOptionId = null;
        foreach ($options as $optionItem) {
            if ($optionItem->getIsDefault()) {
                $defaultOptionId = $optionItem->getId();
                break;
            }
        }
        return $defaultOptionId;
    }

    /**
     * Retrieve array with options ids only
     *
     * @param Option[] $options
     * @return array
     */
    protected function getSwatchesData($options)
    {
        $swatchesData = [];
        foreach ($options as $optionItem) {
            $swatchesData[$optionItem->getId()] = $optionItem->getValue();
        }
        return ['value' => $swatchesData];
    }
}
