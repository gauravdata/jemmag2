<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer;

use Aheadworks\Layerednav\Model\Config;
use Magento\Catalog\Helper\Data as CatalogDataHelper;
use Aheadworks\Layerednav\Model\Layer\Filter\Checker as FilterChecker;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\Checker as FilterItemChecker;
use Magento\Catalog\Model\Category as CategoryModel;
use Aheadworks\Layerednav\Model\Category\Resolver as CategoryResolver;

/**
 * Class Category
 *
 * @package Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer
 */
class Category extends Base
{
    /**
     * @var CatalogDataHelper
     */
    private $catalogDataHelper;

    /**
     * @var array
     */
    private $categories;

    /**
     * @var FilterChecker
     */
    private $filterChecker;

    /**
     * @var CategoryResolver
     */
    private $categoryResolver;

    /**
     * @param Config $config
     * @param FilterItemChecker $filterItemChecker
     * @param CatalogDataHelper $catalogDataHelper
     * @param FilterChecker $filterChecker
     * @param CategoryResolver $categoryResolver
     */
    public function __construct(
        Config $config,
        FilterItemChecker $filterItemChecker,
        CatalogDataHelper $catalogDataHelper,
        FilterChecker $filterChecker,
        CategoryResolver $categoryResolver
    ) {
        parent::__construct($config, $filterItemChecker);
        $this->catalogDataHelper = $catalogDataHelper;
        $this->filterChecker = $filterChecker;
        $this->categoryResolver = $categoryResolver;
    }

    /**
     * Get current category path data
     *
     * @return array
     */
    public function getCurrentCategoryPathData()
    {
        if (empty($this->categories)) {
            $this->categories = $this->catalogDataHelper->getBreadcrumbPath();
            foreach ($this->categories as $index => $categoryData) {
                $categoryLink = $this->getCategoryLink($categoryData);
                if (empty($categoryLink)) {
                    $this->categories[$index]['link'] = '#';
                }
            }
        }

        return $this->categories;
    }

    /**
     * Get category class
     *
     * @param string $categoryIndex
     * @return string
     */
    public function getCategoryClasses($categoryIndex)
    {
        $categoryClasses = '';
        if ($this->isCurrentCategory($categoryIndex)) {
            $categoryClasses .= ' current';
        }
        if ($this->isCategoryFilterApplied()) {
            $categoryClasses .= ' active';
        }
        return $categoryClasses;
    }

    /**
     * Check if index belongs to the current category
     *
     * @param string $categoryIndex
     * @return bool
     */
    public function isCurrentCategory($categoryIndex)
    {
        $categories = $this->getCurrentCategoryPathData();
        end($categories);

        if ($categoryIndex == key($categories)) {
            return true;
        }

        return false;
    }

    /**
     * Check if category filter has been already applied
     *
     * @return bool
     */
    public function isCategoryFilterApplied()
    {
        return $this->filterChecker->isCategoryFilterActive();
    }

    /**
     * Retrieve category link from category data array
     *
     * @param array $categoryData
     * @return string
     */
    public function getCategoryLink($categoryData)
    {
        return isset($categoryData['link']) ? $categoryData['link'] : '';
    }

    /**
     * Retrieve category label from category data array
     *
     * @param array $categoryData
     * @return string
     */
    public function getCategoryLabel($categoryData)
    {
        return isset($categoryData['label']) ? $categoryData['label'] : '';
    }

    /**
     * Check if the category has children categories
     *
     * @param int|string $categoryIdentifier
     * @return bool
     */
    public function hasChildrenCategories($categoryIdentifier)
    {
        $category = $this->getCategory($categoryIdentifier);
        return $category ? $category->hasChildren() : false;
    }

    /**
     * Get category url
     *
     * @param int|string $categoryIdentifier
     * @return string
     */
    public function getCategoryUrl($categoryIdentifier)
    {
        $category = $this->getCategory($categoryIdentifier);
        return $category ? $category->getUrl() : '';
    }

    /**
     * Retrieve category by identifier
     *
     * @param int|string $categoryIdentifier
     * @return CategoryModel|null
     */
    private function getCategory($categoryIdentifier)
    {
        return is_numeric($categoryIdentifier)
            ? $this->categoryResolver->getById($categoryIdentifier)
            : $this->categoryResolver->getByUrlKey($categoryIdentifier);
    }

    /**
     * Check if item is disabled
     *
     * @param FilterItemInterface $filterItem
     * @return bool
     */
    public function isItemDisabled($filterItem)
    {
        return !$filterItem->getCount() && !$this->isActiveItem($filterItem);
    }
}
