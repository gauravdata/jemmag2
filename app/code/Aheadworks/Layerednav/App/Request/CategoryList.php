<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class CategoryList
 * @package Aheadworks\Layerednav\App\Request
 */
class CategoryList
{
    /**
     * @var string
     */
    const CATEGORIES_CACHE_KEY = 'aw_ln_categories';

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Category[]
     */
    private $categories;

    /**
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param CacheInterface $cache
     * @param SerializerInterface $serializer
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        CacheInterface $cache,
        SerializerInterface $serializer
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->cache = $cache;
        $this->serializer = $serializer;
    }

    /**
     * Get categories keyed by url-key
     *
     * @param int|null $parentCategoryId
     * @return array
     */
    public function getCategoriesKeyedByUrlKey($parentCategoryId = null)
    {
        $result = [];
        foreach ($this->getCategories() as $category) {
            if ($parentCategoryId == null || $parentCategoryId == $category['parent_id']) {
                $result[$category['url_key']] = $category;
            }
        }
        return $result;
    }

    /**
     * Get categories keyed by Id
     *
     * @param int|null $parentCategoryId
     * @return array
     */
    public function getCategoriesKeyedById($parentCategoryId = null)
    {
        $result = [];
        foreach ($this->getCategories() as $category) {
            if ($parentCategoryId == null || $parentCategoryId == $category['parent_id']) {
                $result[$category['id']] = $category;
            }
        }
        return $result;
    }

    /**
     * Get category url-keys
     *
     * @param int|null $parentCategoryId
     * @return array
     */
    public function getCategoryUrlKeys($parentCategoryId = null)
    {
        $result = [];
        foreach ($this->getCategories() as $category) {
            if ($parentCategoryId == null || $parentCategoryId == $category['parent_id']) {
                $result[] = $category['url_key'];
            }
        }
        return $result;
    }

    /**
     * Get categories
     *
     * @return array
     */
    private function getCategories()
    {
        if (null === $this->categories) {
            $storeId = $this->storeManager->getStore()->getId();
            $cacheId = self::CATEGORIES_CACHE_KEY . '_' . $storeId;

            $categories = $this->cache->load($cacheId);
            $this->categories = $categories
                ? $this->serializer->unserialize($categories)
                : null;

            if (null === $this->categories) {
                $this->categories = $this->loadCategories($storeId);
                $this->cache->save(
                    $this->serializer->serialize($this->categories),
                    $cacheId,
                    [],
                    null
                );
            }
        }
        return $this->categories;
    }

    /**
     * Load categories from DB
     *
     * @param int $storeId
     * @return array
     */
    private function loadCategories($storeId)
    {
        $collection = $this->collectionFactory->create();
        $collection
            ->addAttributeToSelect('url_key')
            ->setStoreId($storeId)
            ->addIsActiveFilter();

        $categoriesArray = [];
        /** @var Category $category */
        foreach ($collection->getItems() as $category) {
            $categoriesArray[] = [
                'id' => $category->getId(),
                'parent_id' => $category->getParentId(),
                'url_key' => $category->getUrlKey()
            ];
        }

        return $categoriesArray;
    }
}
