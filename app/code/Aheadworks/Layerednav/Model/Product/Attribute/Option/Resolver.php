<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Product\Attribute\Option;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Aheadworks\Layerednav\Model\Product\Attribute\Checker as ProductAttributeChecker;
use Magento\Framework\Validator\UniversalFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection as AttributeOptionCollection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory as AttributeOptionCollectionFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Eav\Api\Data\AttributeOptionLabelInterface;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Magento\Eav\Model\Entity\Attribute\Option;

/**
 * Class Resolver
 *
 * @package Aheadworks\Layerednav\Model\Product\Attribute\Option
 */
class Resolver
{
    /**
     * @var ProductAttributeChecker
     */
    private $productAttributeChecker;

    /**
     * @var UniversalFactory
     */
    private $universalFactory;

    /**
     * @var AttributeOptionCollectionFactory
     */
    private $attributeOptionCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var AttributeOptionLabelInterfaceFactory
     */
    private $attributeOptionLabelFactory;

    /**
     * @param ProductAttributeChecker $productAttributeChecker
     * @param UniversalFactory $universalFactory
     * @param AttributeOptionCollectionFactory $attributeOptionCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param AttributeOptionLabelInterfaceFactory $attributeOptionLabelFactory
     */
    public function __construct(
        ProductAttributeChecker $productAttributeChecker,
        UniversalFactory $universalFactory,
        AttributeOptionCollectionFactory $attributeOptionCollectionFactory,
        StoreManagerInterface $storeManager,
        AttributeOptionLabelInterfaceFactory $attributeOptionLabelFactory
    ) {
        $this->productAttributeChecker = $productAttributeChecker;
        $this->universalFactory = $universalFactory;
        $this->attributeOptionCollectionFactory = $attributeOptionCollectionFactory;
        $this->storeManager = $storeManager;
        $this->attributeOptionLabelFactory = $attributeOptionLabelFactory;
    }

    /**
     * Retrieve ids of default option ids
     *
     * @param ProductAttributeInterface $attribute
     * @return array
     */
    public function getDefaultOptionIds($attribute)
    {
        $defaultOptionIds = [];
        $defaultValue = $attribute->getDefaultValue();
        if (!empty($defaultValue)) {
            $defaultOptionIds = explode(',', $defaultValue);
        }
        return $defaultOptionIds;
    }

    /**
     * Get options array for specific product attribute
     *
     * @param AbstractAttribute $attribute
     * @return Option[]
     */
    public function getByAttribute($attribute)
    {
        $options = [];

        if (!$this->productAttributeChecker->isSourceModelUsed($attribute)) {
            /** @var AttributeOptionCollection $attributeOptionCollection */
            $attributeOptionCollection = $this->attributeOptionCollectionFactory->create();
            /** @var Option[] $options */
            $options = $attributeOptionCollection
                ->setAttributeFilter($attribute->getId())
                ->setPositionOrder('asc', true)
                ->setStoreFilter(Store::DEFAULT_STORE_ID)
                ->getItems();

            $options = $this->addStoreLabels($attribute->getId(), $options);
            if ($this->productAttributeChecker->areNativeVisualSwatchesUsed($attribute)) {
                $options = $this->addVisualSwatchesValues($attribute->getId(), $options);
            }
        }

        return $options;
    }

    /**
     * Add store labels to the array of attribute options
     *
     * @param int $attributeId
     * @param Option[] $options
     * @return Option[]
     */
    protected function addStoreLabels($attributeId, $options)
    {
        $stores = $this->storeManager->getStores(true);
        foreach ($stores as $store) {
            $optionsLabelsForStore = $this->getOptionsLabelsForStore($attributeId, $store->getId());
            foreach ($options as $optionItem) {
                if (isset($optionsLabelsForStore[$optionItem->getId()])) {
                    $optionStoreLabels =
                        empty($optionItem->getStoreLabels())
                            ? []
                            : $optionItem->getStoreLabels();
                    /** @var AttributeOptionLabelInterface $storeLabel */
                    $storeLabel = $this->attributeOptionLabelFactory->create();
                    $storeLabel->setStoreId($store->getId());
                    $storeLabel->setLabel($optionsLabelsForStore[$optionItem->getId()]);

                    $optionStoreLabels[] = $storeLabel;
                    $optionItem->setStoreLabels($optionStoreLabels);
                }
            }
        }
        return $options;
    }

    /**
     * Retrieve array of attribute options labels for specific store view, where key is option id, and value - label
     *
     * @param int  $attributeId
     * @param int $storeId
     * @return array
     */
    protected function getOptionsLabelsForStore($attributeId, $storeId)
    {
        $optionsLabelsForStore = [];
        /** @var AttributeOptionCollection $optionCollectionForStoreView */
        $optionCollectionForStoreView = $this->attributeOptionCollectionFactory->create();
        /** @var Option[] $optionsForStoreView */
        $optionsForStoreView = $optionCollectionForStoreView
            ->setAttributeFilter($attributeId)
            ->setStoreFilter($storeId, false)
            ->load();
        foreach ($optionsForStoreView as $optionItem) {
            $optionsLabelsForStore[$optionItem->getId()] = $optionItem->getValue();
        }
        return $optionsLabelsForStore;
    }

    /**
     * Add visual swatches values to the array of attribute options
     *
     * @param int $attributeId
     * @param Option[] $options
     * @return Option[]
     */
    protected function addVisualSwatchesValues($attributeId, $options)
    {
        $visualSwatchesValues = [];
        /** @var AttributeOptionCollection $optionCollection */
        $optionCollection = $this->attributeOptionCollectionFactory->create();
        $optionCollection
            ->setAttributeFilter($attributeId)
            ->setStoreFilter(Store::DEFAULT_STORE_ID, false);
        $optionCollection->getSelect()->joinLeft(
            ['swatch_table' => $optionCollection->getTable('eav_attribute_option_swatch')],
            'swatch_table.option_id = main_table.option_id 
            AND swatch_table.store_id = '. Store::DEFAULT_STORE_ID,
            'swatch_table.value AS label'
        );
        foreach ($optionCollection as $optionItem) {
            $visualSwatchesValues[$optionItem->getId()] = $optionItem->getLabel();
        }
        foreach ($options as $optionItem) {
            if (isset($visualSwatchesValues[$optionItem->getId()])) {
                $optionItem->setValue($visualSwatchesValues[$optionItem->getId()]);
            }
        }

        return $options;
    }
}
