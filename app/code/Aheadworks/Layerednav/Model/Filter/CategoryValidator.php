<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category as CategoryModel;

/**
 * Class CategoryValidator
 * @package Aheadworks\Layerednav\Model\Filter
 */
class CategoryValidator
{
    /**
     * Check if the filter object valid for the current category
     *
     * @param FilterInterface $filter
     * @param CategoryInterface|CategoryModel $category
     * @return bool
     */
    public function validate($filter, $category)
    {
        switch ($filter->getCategoryMode()) {
            case FilterInterface::CATEGORY_MODE_EXCLUDE:
                if (is_array($filter->getExcludeCategoryIds())) {
                    return !in_array($category->getId(), $filter->getExcludeCategoryIds());
                }
                break;

            case FilterInterface::CATEGORY_MODE_LOWEST_LEVEL:
                return !$category->hasChildren();

            case FilterInterface::CATEGORY_MODE_ALL:
            default:
        }

        return true;
    }
}
