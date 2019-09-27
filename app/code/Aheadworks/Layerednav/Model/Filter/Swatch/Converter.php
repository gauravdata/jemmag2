<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter\Swatch;

use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Magento\Eav\Model\Entity\Attribute\Option;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Eav\Api\Data\AttributeOptionLabelInterface;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Aheadworks\Layerednav\Model\StorefrontValueResolver;
use Magento\Store\Model\Store;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;

/**
 * Class Converter
 *
 * @package Aheadworks\Layerednav\Model\Filter\Swatch
 */
class Converter
{
    /**
     * @var AttributeOptionInterfaceFactory
     */
    private $attributeOptionFactory;

    /**
     * @var AttributeOptionLabelInterfaceFactory
     */
    private $attributeOptionLabelFactory;

    /**
     * @var StorefrontValueResolver
     */
    private $storefrontValueResolver;

    /**
     * @param AttributeOptionInterfaceFactory $attributeOptionFactory
     * @param AttributeOptionLabelInterfaceFactory $attributeOptionLabelFactory
     * @param StorefrontValueResolver $storefrontValueResolver
     */
    public function __construct(
        AttributeOptionInterfaceFactory $attributeOptionFactory,
        AttributeOptionLabelInterfaceFactory $attributeOptionLabelFactory,
        StorefrontValueResolver $storefrontValueResolver
    ) {
        $this->attributeOptionFactory = $attributeOptionFactory;
        $this->attributeOptionLabelFactory = $attributeOptionLabelFactory;
        $this->storefrontValueResolver = $storefrontValueResolver;
    }

    /**
     * Convert swatch item to the standard attribute option object
     *
     * @param SwatchInterface $filterSwatchItem
     * @return Option
     */
    public function toAttributeOption($filterSwatchItem)
    {
        /** @var Option $attributeOption */
        $attributeOption = $this->attributeOptionFactory->create();

        $actualCurrentStorefrontTitle = $this->storefrontValueResolver->getStorefrontValue(
            $filterSwatchItem->getStorefrontTitles(),
            Store::DEFAULT_STORE_ID
        );
        $attributeOption->setLabel($actualCurrentStorefrontTitle);
        $attributeOption->setSortOrder($filterSwatchItem->getSortOrder());
        $attributeOption->setIsDefault($filterSwatchItem->getIsDefault());
        $attributeOption->setId($filterSwatchItem->getOptionId());
        $attributeOption->setValue($filterSwatchItem->getOptionId());

        if (is_array($filterSwatchItem->getStorefrontTitles())) {
            $storeLabels = $this->getStoreLabels($filterSwatchItem->getStorefrontTitles());
            $attributeOption->setStoreLabels($storeLabels);
        }

        return $attributeOption;
    }

    /**
     * Convert swatch item to the swatch attribute option object
     *
     * @param SwatchInterface $filterSwatchItem
     * @return Option
     */
    public function toSwatchAttributeOption($filterSwatchItem)
    {
        /** @var Option $attributeOption */
        $attributeOption = $this->attributeOptionFactory->create();

        $actualCurrentStorefrontTitle = $this->storefrontValueResolver->getStorefrontValue(
            $filterSwatchItem->getStorefrontTitles(),
            Store::DEFAULT_STORE_ID
        );
        $attributeOption->setLabel($actualCurrentStorefrontTitle);
        $attributeOption->setSortOrder($filterSwatchItem->getSortOrder());
        $attributeOption->setIsDefault($filterSwatchItem->getIsDefault());
        $attributeOption->setId($filterSwatchItem->getOptionId());
        $attributeOption->setValue($filterSwatchItem->getValue());

        if (is_array($filterSwatchItem->getStorefrontTitles())) {
            $storeLabels = $this->getStoreLabels($filterSwatchItem->getStorefrontTitles());
            $attributeOption->setStoreLabels($storeLabels);
        }

        return $attributeOption;
    }

    /**
     * Retrieve array of attribute option labels by corresponding storefront titles array
     *
     * @param StoreValueInterface[] $storefrontTitles
     * @return AttributeOptionLabelInterface[]
     */
    protected function getStoreLabels($storefrontTitles)
    {
        $storeLabels = [];
        foreach ($storefrontTitles as $titleItem) {
            /** @var AttributeOptionLabelInterface $storeLabel */
            $storeLabel = $this->attributeOptionLabelFactory->create();
            $storeLabel->setStoreId($titleItem->getStoreId());
            $storeLabel->setLabel($titleItem->getValue());
            $storeLabels[] = $storeLabel;
        }

        return $storeLabels;
    }
}
