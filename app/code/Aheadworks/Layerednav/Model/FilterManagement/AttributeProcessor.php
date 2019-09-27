<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\FilterManagement;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterfaceFactory;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Api\Data\AttributeOptionLabelInterface;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Magento\Store\Model\Store;

/**
 * Class AttributeProcessor
 * @package Aheadworks\Layerednav\Model\FilterManagement
 */
class AttributeProcessor
{
    /**
     * @var StoreValueInterfaceFactory
     */
    private $storeValueFactory;

    /**
     * @var AttributeOptionLabelInterfaceFactory
     */
    private $attributeOptionLabelFactory;

    /**
     * @param StoreValueInterfaceFactory $storeValueFactory
     * @param AttributeOptionLabelInterfaceFactory $AttributeOptionLabelFactory
     */
    public function __construct(
        StoreValueInterfaceFactory $storeValueFactory,
        AttributeOptionLabelInterfaceFactory $AttributeOptionLabelFactory
    ) {
        $this->storeValueFactory = $storeValueFactory;
        $this->attributeOptionLabelFactory = $AttributeOptionLabelFactory;
    }

    /**
     * Get storefront titles
     *
     * @param ProductAttributeInterface $attribute
     * @return array
     */
    public function getStorefrontTitles(ProductAttributeInterface $attribute)
    {
        $attributeLabels = $attribute->getStoreLabels();
        $titles = [];
        foreach ($attributeLabels as $storeId => $attributeLabel) {
            if ($attributeLabel) {
                /** @var StoreValueInterface $sortOrderValue */
                $titleValue = $this->storeValueFactory->create();
                $titleValue
                    ->setStoreId($storeId)
                    ->setValue($attributeLabel);

                $titles[] = $titleValue;
            }
        }

        return $titles;
    }

    /**
     * Get attribute labels
     *
     * @param FilterInterface $filter
     * @return array
     */
    public function getAttributeLabels($filter)
    {
        /** @var StoreValueInterface[] $titles */
        $titles = $filter->getStorefrontTitles();
        $labels = [];
        /** @var StoreValueInterface $title */
        foreach ($titles as $title) {
            /** @var AttributeOptionLabelInterface $labelOption */
            $labelOption = $this->attributeOptionLabelFactory->create();
            $labelOption
                ->setStoreId($title->getStoreId())
                ->setLabel($title->getValue());

            $labels[] = $labelOption;
        }

        return $labels;
    }

    /**
     * @param ProductAttributeInterface $attribute
     * @param FilterInterface $filter
     * @return bool
     */
    public function isLabelsDifferent($attribute, $filter)
    {
        $attributeLabels = $attribute->getStoreLabels();
        if (!isset($attributeLabels[Store::DEFAULT_STORE_ID])) {
            $attributeLabels[Store::DEFAULT_STORE_ID] = $attribute->getDefaultFrontendLabel();
        }

        $filterTitles[Store::DEFAULT_STORE_ID] = $filter->getDefaultTitle();
        /** @var StoreValueInterface $titleValue */
        foreach ($filter->getStorefrontTitles() as $titleValue) {
            $filterTitles[$titleValue->getStoreId()] = $titleValue->getValue();
        }

        return ($attributeLabels != $filterTitles);
    }
}
