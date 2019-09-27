<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterfaceFactory;
use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Aheadworks\Layerednav\Api\Data\FilterCategoryInterfaceFactory;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Aheadworks\Layerednav\Api\FilterManagementInterface;
use Aheadworks\Layerednav\Model\FilterManagement\AttributeProcessor;
use Aheadworks\Layerednav\Model\Source\Filter\Types as FilterTypesSource;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterfaceFactory;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Layerednav\Model\Product\Attribute\Checker as ProductAttributeChecker;
use Aheadworks\Layerednav\Model\Filter\Processor as FilterProcessor;
use Aheadworks\Layerednav\Model\Product\Attribute\Processor as ProductAttributeProcessor;

/**
 * Class FilterManagement
 * @package Aheadworks\Layerednav\Model
 */
class FilterManagement implements FilterManagementInterface
{
    /**
     * Filterable with results value
     */
    const FILTERABLE_WITH_RESULTS = 1;

    /**
     * Filterable in search
     */
    const FILTERABLE_IN_SEARCH = 1;

    /**
     * Flag for native attribute object to prevent excessive synchronization
     */
    const NO_NEED_TO_SYNCHRONIZE_FILTER_FLAG = 'aw_ln_no_need_to_synchronize';

    /**
     * @var AttributeCollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var FilterInterfaceFactory
     */
    private $filterFactory;

    /**
     * @var FilterRepositoryInterface
     */
    private $filterRepository;

    /**
     * @var FilterCategoryInterfaceFactory
     */
    private $filterCategoryFactory;

    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterTypesSource
     */
    private $filterTypesSource;

    /**
     * @var StoreValueInterfaceFactory
     */
    private $storeValueFactory;

    /**
     * @var AttributeProcessor
     */
    private $attributeProcessor;

    /**
     * @var ProductAttributeChecker
     */
    private $productAttributeChecker;

    /**
     * @var FilterProcessor
     */
    private $filterProcessor;

    /**
     * @var ProductAttributeProcessor
     */
    private $productAttributeProcessor;

    /**
     * @param AttributeCollectionFactory $attributeCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param FilterInterfaceFactory $filterFactory
     * @param FilterRepositoryInterface $filterRepository
     * @param FilterCategoryInterfaceFactory $filterCategoryFactory
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterTypesSource $filterTypesSource
     * @param StoreValueInterfaceFactory $storeValueFactory
     * @param AttributeProcessor $attributeProcessor
     * @param ProductAttributeChecker $productAttributeChecker
     * @param FilterProcessor $filterProcessor
     * @param ProductAttributeProcessor $productAttributeProcessor
     */
    public function __construct(
        AttributeCollectionFactory $attributeCollectionFactory,
        StoreManagerInterface $storeManager,
        FilterInterfaceFactory $filterFactory,
        FilterRepositoryInterface $filterRepository,
        FilterCategoryInterfaceFactory $filterCategoryFactory,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterTypesSource $filterTypesSource,
        StoreValueInterfaceFactory $storeValueFactory,
        AttributeProcessor $attributeProcessor,
        ProductAttributeChecker $productAttributeChecker,
        FilterProcessor $filterProcessor,
        ProductAttributeProcessor $productAttributeProcessor
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->storeManager = $storeManager;
        $this->filterFactory = $filterFactory;
        $this->filterRepository = $filterRepository;
        $this->filterCategoryFactory = $filterCategoryFactory;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterTypesSource = $filterTypesSource;
        $this->storeValueFactory = $storeValueFactory;
        $this->attributeProcessor = $attributeProcessor;
        $this->productAttributeChecker = $productAttributeChecker;
        $this->filterProcessor = $filterProcessor;
        $this->productAttributeProcessor = $productAttributeProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function createFilter($attribute)
    {
        if ($attribute->getIsFilterable() || $attribute->getIsFilterableInSearch()) {
            /** @var FilterInterface $filter */
            $filter = $this->filterFactory->create();

            /** @var StoreValueInterface $sortOrderValue */
            $sortOrderValue = $this->storeValueFactory->create();
            $sortOrderValue
                ->setStoreId(Store::DEFAULT_STORE_ID)
                ->setValue(FilterInterface::SORT_ORDER_MANUAL);

            $filter
                ->setCode($attribute->getAttributeCode())
                ->setType($this->getAttributeFilterType($attribute))
                ->setIsFilterable($attribute->getIsFilterable())
                ->setIsFilterableInSearch($attribute->getIsFilterableInSearch())
                ->setPosition($attribute->getPosition())
                ->setDefaultTitle($attribute->getDefaultFrontendLabel())
                ->setStorefrontTitles($this->attributeProcessor->getStorefrontTitles($attribute))
                ->setSortOrders([$sortOrderValue])
                ->setCategoryMode(FilterInterface::CATEGORY_MODE_ALL);

            $this->filterProcessor->setSwatchesByAttribute($filter, $attribute);

            $this->filterRepository->save($filter, null, false);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isSyncNeeded($filter, $attribute)
    {
        return ($this->attributeProcessor->isLabelsDifferent($attribute, $filter)
            || $attribute->getIsFilterable() != $filter->getIsFilterable()
            || $attribute->getIsFilterableInSearch() != $filter->getIsFilterableInSearch()
            || $attribute->getPosition() != $filter->getPosition()
            || $this->productAttributeChecker->areExtraSwatchesAllowed($attribute)
        ) && !$this->productAttributeChecker->areNativeVisualSwatchesUsed($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function synchronizeFilterById($filterId)
    {
        try {
            /** @var FilterInterface $filter */
            $filter = $this->filterRepository->get($filterId);
            if ($this->isAttributeBased($filter)) {
                try {
                    /** @var ProductAttributeInterface $attribute */
                    $attribute = $this->productAttributeRepository->get($filter->getCode());
                    $this->synchronizeFilter($filter, $attribute);
                } catch (NoSuchEntityException $e) {
                    $this->filterRepository->delete($filter);
                    return false;
                }
            }
        } catch (NoSuchEntityException $e) {
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function synchronizeFilter($filter, $attribute)
    {
        if ($filter->getCode() == $attribute->getAttributeCode()) {
            if ($attribute->getIsFilterable() || $attribute->getIsFilterableInSearch()) {
                $filter
                    ->setType($this->getAttributeFilterType($attribute))
                    ->setIsFilterable($attribute->getIsFilterable())
                    ->setIsFilterableInSearch($attribute->getIsFilterableInSearch())
                    ->setPosition($attribute->getPosition())
                    ->setDefaultTitle($attribute->getDefaultFrontendLabel())
                    ->setStorefrontTitles($this->attributeProcessor->getStorefrontTitles($attribute));

                $this->filterProcessor->setSwatchesByAttribute($filter, $attribute);

                try {
                    $this->filterRepository->save($filter, null, false);
                } catch (CouldNotSaveException $e) {
                    return false;
                }
            } else {
                try {
                    $this->filterRepository->delete($filter);
                } catch (NoSuchEntityException $e) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function synchronizeAttribute($filterId, $ignoreFilterType = false)
    {
        try {
            /** @var FilterInterface $filter */
            $filter = $this->filterRepository->get($filterId);
            if (in_array($filter->getType(), FilterInterface::ATTRIBUTE_FILTER_TYPES)) {
                /** @var ProductAttributeInterface $attribute */
                $attribute = $this->productAttributeRepository->get($filter->getCode());

                if ($this->isSyncNeeded($filter, $attribute)) {
                    $attribute
                        ->setDefaultFrontendLabel($filter->getDefaultTitle())
                        ->setFrontendLabels($this->attributeProcessor->getAttributeLabels($filter))
                        ->setIsFilterable($filter->getIsFilterable())
                        ->setIsFilterableInSearch($filter->getIsFilterableInSearch())
                        ->setPosition($filter->getPosition());

                    $this->productAttributeProcessor->setOptionsByFilter($attribute, $filter);

                    $attribute->setData(self::NO_NEED_TO_SYNCHRONIZE_FILTER_FLAG, true);

                    $this->productAttributeRepository->save($attribute);
                }
            } elseif (!$ignoreFilterType) {
                return false;
            }
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function synchronizeCustomFilters()
    {
        $customFilterTypes = FilterInterface::CUSTOM_FILTER_TYPES;

        $this->searchCriteriaBuilder
            ->addFilter(FilterInterface::TYPE, $customFilterTypes, 'in');

        /** @var FilterInterface[] $filters */
        $filters = $this->filterRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        foreach ($filters as $filter) {
            $resultIndex = array_search($filter->getType(), $customFilterTypes);
            if ($resultIndex !== false) {
                unset($customFilterTypes[$resultIndex]);
            }
        }
        foreach ($customFilterTypes as $customFilterType) {
            $this->addCustomFilter($customFilterType);
        }
    }

    /**
     * Add custom filter
     *
     * @param string $type
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function addCustomFilter($type)
    {
        /** @var StoreValueInterface $sortOrderValue */
        $sortOrderValue = $this->storeValueFactory->create();
        $sortOrderValue
            ->setStoreId(Store::DEFAULT_STORE_ID)
            ->setValue(FilterInterface::SORT_ORDER_MANUAL);

        /** @var FilterInterface|Filter $filter */
        $filter = $this->filterFactory->create();
        $filter
            ->setCode($this->getCustomRequestVarCode($type))
            ->setType($type)
            ->setIsFilterable(self::FILTERABLE_WITH_RESULTS)
            ->setIsFilterableInSearch(self::FILTERABLE_IN_SEARCH)
            ->setPosition(0)
            ->setDefaultTitle($this->filterTypesSource->getOptionByValue($type))
            ->setSortOrders([$sortOrderValue])
            ->setCategoryMode(FilterInterface::CATEGORY_MODE_ALL);

        if ($type == FilterInterface::CATEGORY_FILTER) {
            /** @var FilterCategoryInterface $filterCategory */
            $filterCategory = $this->filterCategoryFactory->create();
            /** @var StoreValueInterface $sortOrderValue */
            $listStyleValue = $this->storeValueFactory->create();
            $listStyleValue
                ->setStoreId(Store::DEFAULT_STORE_ID)
                ->setValue(FilterCategoryInterface::CATEGORY_STYLE_DEFAULT);

            $filterCategory->setListStyles([$listStyleValue]);
            $filter->setCategoryFilterData($filterCategory);
        }

        $this->filterRepository->save($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function synchronizeAttributeFilters()
    {
        $attributesToSync = [];
        $categoryAttributes = $this->getCategoryFilterableAttributes();
        /** @var ProductAttributeInterface $attribute */
        foreach ($categoryAttributes as $attribute) {
            $attributesToSync[$attribute->getAttributeId()] = $attribute;
        }
        $searchAttributes = $this->getSearchFilterableAttributes();
        /** @var ProductAttributeInterface $attribute */
        foreach ($searchAttributes as $attribute) {
            $attributesToSync[$attribute->getAttributeId()] = $attribute;
        }
        $syncFilterIds = [];
        /** @var ProductAttributeInterface $attribute */
        foreach ($attributesToSync as $attribute) {
            try {
                /** @var FilterInterface $filter */
                $filter = $this->filterRepository->getByCode(
                    $attribute->getAttributeCode(),
                    $this->getAttributeFilterType($attribute)
                );
            } catch (NoSuchEntityException $e) {
                $filter = $this->filterFactory->create();

                /** @var StoreValueInterface $sortOrderValue */
                $sortOrderValue = $this->storeValueFactory->create();
                $sortOrderValue
                    ->setStoreId(Store::DEFAULT_STORE_ID)
                    ->setValue(FilterInterface::SORT_ORDER_MANUAL);

                $filter
                    ->setSortOrders([$sortOrderValue])
                    ->setCategoryMode(FilterInterface::CATEGORY_MODE_ALL);
            }

            $filter
                ->setCode($attribute->getAttributeCode())
                ->setType($this->getAttributeFilterType($attribute))
                ->setIsFilterable($attribute->getIsFilterable())
                ->setIsFilterableInSearch($attribute->getIsFilterableInSearch())
                ->setPosition($attribute->getPosition())
                ->setDefaultTitle($attribute->getDefaultFrontendLabel())
                ->setStorefrontTitles($this->attributeProcessor->getStorefrontTitles($attribute));

            $this->filterProcessor->setSwatchesByAttribute($filter, $attribute);

            $filter = $this->filterRepository->save($filter, null, false);

            $syncFilterIds[] = $filter->getId();
        }
        $this->removeObsoleteAttributeFilters($syncFilterIds);
    }

    /**
     * Get filterable attributes in category
     *
     * @return AttributeCollection
     */
    private function getCategoryFilterableAttributes()
    {
        /** @var $collection AttributeCollection */
        $collection = $this->attributeCollectionFactory->create();

        $collection->setItemObjectClass(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class)
            ->addStoreLabel($this->storeManager->getStore()->getId())
            ->addIsFilterableFilter();

        return $collection;
    }

    /**
     * Get filterable attributes in search
     *
     * @return AttributeCollection
     */
    private function getSearchFilterableAttributes()
    {
        /** @var $collection AttributeCollection */
        $collection = $this->attributeCollectionFactory->create();

        $collection->setItemObjectClass(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class)
            ->addStoreLabel($this->storeManager->getStore()->getId())
            ->addIsFilterableInSearchFilter()
            ->addVisibleFilter();

        return $collection;
    }

    /**
     * Remove obsolete attribute filters
     *
     * @param array $actualFilterIds
     */
    private function removeObsoleteAttributeFilters($actualFilterIds)
    {
        $this->searchCriteriaBuilder
            ->addFilter(FilterInterface::ID, $actualFilterIds, 'nin')
            ->addFilter(FilterInterface::TYPE, FilterInterface::ATTRIBUTE_FILTER_TYPES, 'in');

        /** @var FilterInterface[] $obsoleteFilters */
        $obsoleteFilters = $this->filterRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        /** @var FilterInterface $filter */
        foreach ($obsoleteFilters as $filter) {
            $this->filterRepository->delete($filter);
        }
    }

    /**
     * Get attribute filter type
     *
     * @param ProductAttributeInterface $attribute
     * @return string
     */
    public function getAttributeFilterType($attribute)
    {
        $filterType = FilterInterface::ATTRIBUTE_FILTER;
        if ($attribute->getAttributeCode() == 'price') {
            $filterType = FilterInterface::PRICE_FILTER;
        } elseif ($attribute->getBackendType() == 'decimal') {
            $filterType = FilterInterface::DECIMAL_FILTER;
        }
        return $filterType;
    }

    /**
     * Check if the filter is an attribute based
     *
     * @param FilterInterface $filter
     * @return bool
     */
    private function isAttributeBased(FilterInterface $filter)
    {
        switch ($filter->getType()) {
            case FilterInterface::ATTRIBUTE_FILTER:
            case FilterInterface::PRICE_FILTER:
            case FilterInterface::DECIMAL_FILTER:
                return true;
        }
        return false;
    }

    /**
     * Get custom filter request variable code
     *
     * @param string$type
     * @return string
     */
    private function getCustomRequestVarCode($type)
    {
        foreach (FilterInterface::CUSTOM_FILTER_TYPES as $key => $value) {
            if ($type == $value) {
                return $key;
            }
        }
        return '';
    }
}
