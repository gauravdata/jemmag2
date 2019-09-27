<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Product\Attribute\Option;

use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Magento\Eav\Model\Entity\Attribute\Option;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterfaceFactory;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterfaceFactory;
use Magento\Eav\Api\Data\AttributeOptionLabelInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Aheadworks\Layerednav\Model\Product\Attribute\Option\Resolver as AttributeOptionResolver;

/**
 * Class Converter
 *
 * @package Aheadworks\Layerednav\Model\Product\Attribute\Option
 */
class Converter
{
    /**
     * @var SwatchInterfaceFactory
     */
    private $swatchFactory;

    /**
     * @var StoreValueInterfaceFactory
     */
    private $storeValueFactory;

    /**
     * @var AttributeOptionResolver
     */
    private $attributeOptionResolver;

    /**
     * @param SwatchInterfaceFactory $swatchFactory
     * @param StoreValueInterfaceFactory $storeValueFactory
     * @param AttributeOptionResolver $attributeOptionResolver
     */
    public function __construct(
        SwatchInterfaceFactory $swatchFactory,
        StoreValueInterfaceFactory $storeValueFactory,
        AttributeOptionResolver $attributeOptionResolver
    ) {
        $this->swatchFactory = $swatchFactory;
        $this->storeValueFactory = $storeValueFactory;
        $this->attributeOptionResolver = $attributeOptionResolver;
    }

    /**
     * Convert standard attribute option to the swatch item
     *
     * @param ProductAttributeInterface $attribute
     * @param Option $attributeOption
     * @return SwatchInterface
     */
    public function toFilterSwatchItem($attribute, $attributeOption)
    {
        /** @var SwatchInterface $filterSwatchItem */
        $filterSwatchItem = $this->swatchFactory->create();

        $filterSwatchItem->setSortOrder($attributeOption->getSortOrder());
        $filterSwatchItem->setOptionId($attributeOption->getId());
        $filterSwatchItem->setValue($attributeOption->getValue());

        $defaultOptionIds = $this->attributeOptionResolver->getDefaultOptionIds($attribute);
        $filterSwatchItem->setIsDefault(in_array($attributeOption->getId(), $defaultOptionIds));

        $storeLabels = $attributeOption->getStoreLabels();
        if (is_array($storeLabels)) {
            $storefrontTitles = [];
            /** @var AttributeOptionLabelInterface $label */
            foreach ($storeLabels as $label) {
                /** @var StoreValueInterface $title */
                $title = $this->storeValueFactory->create();
                $title->setStoreId($label->getStoreId());
                $title->setValue($label->getLabel());
                $storefrontTitles[] = $title;
            }
            $filterSwatchItem->setStorefrontTitles($storefrontTitles);
        }
        return $filterSwatchItem;
    }
}
