<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\CacheInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class AttributeList
 * @package Aheadworks\Layerednav\App\Request
 */
class AttributeList
{
    /**#@+
     * Attribute list types
     */
    const LIST_TYPE_DEFAULT = 'default';
    const LIST_TYPE_DECIMAL = 'decimal';
    /**#@-*/

    /**
     * @var string
     */
    const ATTRIBUTES_CACHE_KEY = 'aw_ln_attributes';

    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CacheInterface $cache
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ProductAttributeRepositoryInterface $productAttributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CacheInterface $cache,
        SerializerInterface $serializer
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cache = $cache;
        $this->serializer = $serializer;
    }

    /**
     * Remove cached attributes data
     *
     * @return bool
     */
    public function flushAttributesCache()
    {
        return $this->cache->remove(self::ATTRIBUTES_CACHE_KEY);
    }

    /**
     * Get attribute list keyed by attribute code
     *
     * @param string $listType
     * @return array
     */
    public function getAttributesKeyedByCode($listType = self::LIST_TYPE_DEFAULT)
    {
        $result = [];
        foreach ($this->getAttributes($listType) as $attribute) {
            $result[$attribute['code']] = $attribute;
        }
        return $result;
    }

    /**
     * Get attribute codes
     *
     * @param string $listType
     * @return array
     */
    public function getAttributeCodes($listType = self::LIST_TYPE_DEFAULT)
    {
        $result = [];
        foreach ($this->getAttributes($listType) as $attribute) {
            $result[] = $attribute['code'];
        }
        return $result;
    }

    /**
     * Get attributes from cache
     *
     * @param string $listType
     * @return array
     */
    private function getAttributes($listType = self::LIST_TYPE_DEFAULT)
    {
        if (!isset($this->attributes[$listType])) {
            $attributes = $this->cache->load(self::ATTRIBUTES_CACHE_KEY);
            $this->attributes = $attributes
                ? $this->serializer->unserialize($attributes)
                : [];

            if (!isset($this->attributes[$listType])) {
                $this->attributes[$listType] = $this->loadAttributes($listType);
                $this->cache->save(
                    $this->serializer->serialize($this->attributes),
                    self::ATTRIBUTES_CACHE_KEY,
                    [],
                    null
                );
            }
        }
        return $this->attributes[$listType];
    }

    /**
     * Load attributes from DB
     *
     * @param string $listType
     * @return array
     */
    private function loadAttributes($listType)
    {
        $this->searchCriteriaBuilder
            ->addFilter(ProductAttributeInterface::IS_VISIBLE, true)
            ->addFilter(ProductAttributeInterface::IS_FILTERABLE, true);
        if ($listType == self::LIST_TYPE_DEFAULT) {
            $this->searchCriteriaBuilder
                ->addFilter(ProductAttributeInterface::ATTRIBUTE_CODE, 'price', 'neq')
                ->addFilter(ProductAttributeInterface::BACKEND_TYPE, 'decimal', 'neq');
        } elseif ($listType == self::LIST_TYPE_DECIMAL) {
            $this->searchCriteriaBuilder
                ->addFilter(ProductAttributeInterface::BACKEND_TYPE, 'decimal', 'eq');
        }
        $attributes = $this->productAttributeRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();

        $attributesArray = [];
        /** @var ProductAttributeInterface|EavAttribute $attribute */
        foreach ($attributes as $attribute) {
            $attributesArray[] = [
                'code' => $attribute->getAttributeCode(),
                'options' => $this->getPreparedOptionsArray($attribute),
                'select_options' => $this->getPreparedSelectOptionsArray($attribute),
            ];
        }

        return $attributesArray;
    }

    /**
     * Retrieve array of prepared attribute options
     *
     * @param ProductAttributeInterface|EavAttribute $attribute
     * @return array
     */
    private function getPreparedOptionsArray($attribute)
    {
        $optionsData = [];
        /** @var AttributeOptionInterface $options */
        $options = $attribute->getOptions();
        /** @var AttributeOptionInterface $optionItem */
        foreach ($options as $optionItem) {
            $optionsData[] = [
                AttributeOptionInterface::VALUE => (string)$optionItem->getValue(),
                AttributeOptionInterface::LABEL => (string)$optionItem->getLabel(),
            ];
        }
        return $optionsData;
    }

    /**
     * Retrieve array of prepared select options for attribute
     *
     * @param ProductAttributeInterface|EavAttribute $attribute
     * @return array
     */
    private function getPreparedSelectOptionsArray($attribute)
    {
        $optionsData = [];
        $selectOptions = $attribute->getFrontend()->getSelectOptions();
        foreach ($selectOptions as $optionItem) {
            $optionsData[] = [
                'value' => (string)(isset($optionItem['value']) ? $optionItem['value'] : ''),
                'label' => (string)(isset($optionItem['label']) ? $optionItem['label'] : ''),
            ];
        }
        return $optionsData;
    }
}
