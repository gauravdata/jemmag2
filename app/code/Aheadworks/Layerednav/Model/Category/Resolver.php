<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Category;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

/**
 * Class Resolver
 * @package Aheadworks\Layerednav\Model\Category
 */
class Resolver
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryCollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Retrieve category by id
     *
     * @param int $categoryId
     * @return CategoryInterface|null
     */
    public function getById($categoryId)
    {
        try {
            /** @var CategoryInterface $category */
            $category = $this->categoryRepository->get($categoryId);
        } catch (NoSuchEntityException $e) {
            $category = null;
        }
        return $category;
    }

    /**
     * Get category name
     *
     * @param int $categoryId
     * @return string
     */
    public function getCategoryName($categoryId)
    {
        $name = '';
        $category = $this->getById($categoryId);
        if ($category) {
            $name = $category->getName();
        }
        return $name;
    }

    /**
     * Retrieve category by url key
     *
     * @param string $categoryUrlKey
     * @return CategoryInterface|null
     */
    public function getByUrlKey($categoryUrlKey)
    {
        try {
            /** @var CategoryCollection $categoryCollection */
            $categoryCollection = $this->categoryCollectionFactory->create();

            /** @var CategoryModel $categoryItem */
            $categoryItem = $categoryCollection
                ->addAttributeToFilter('url_key', $categoryUrlKey)
                ->addUrlRewriteToResult()
                ->getFirstItem();

            $category = $this->getById($categoryItem->getCategoryId());
        } catch (LocalizedException $e) {
            $category = null;
        }

        return $category;
    }

    /**
     * Get active category ids
     *
     * @param array $ids
     * @param int $storeId
     * @return int[]|false
     */
    public function getActiveCategoryIds($ids, $storeId)
    {
        $categoryIds = [];
        foreach ($ids as $categoryId) {
            if ($this->isActive($categoryId, $storeId)) {
                $categoryIds[] = $categoryId;
            }
        }
        return !empty($categoryIds) ? $categoryIds : false;
    }

    /**
     * Check if category active (include all parent categories)
     *
     * @param int $categoryId
     * @param int $storeId
     * @return bool
     */
    private function isActive($categoryId, $storeId)
    {
        try {
            $category = $this->categoryRepository->get($categoryId, $storeId);
            if ($category->getLevel() != 0) {
                if (!$category->getIsActive()) {
                    return false;
                }
                return $this->isActive($category->getParentId(), $storeId);
            }
        } catch (NoSuchEntityException $e) {
            return false;
        }
        return true;
    }

    /**
     * Get category url-keys
     *
     * @param array $categoryIds
     * @return array
     */
    public function getCategoryUrlKeys($categoryIds)
    {
        $result = [];
        foreach ($categoryIds as $id) {
            try {
                $result[] = is_numeric($id)
                    ? $this->categoryRepository->get($id)->getUrlKey()
                    : $id;
            } catch (NoSuchEntityException $e) {
                // do nothing
            }
        }
        return $result;
    }
}
