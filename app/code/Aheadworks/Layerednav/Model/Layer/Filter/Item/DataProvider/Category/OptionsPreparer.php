<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Category;

use Aheadworks\Layerednav\Model\Category\Resolver as CategoryResolver;
use Aheadworks\Layerednav\Model\Seo\Checker as SeoChecker;
use Magento\Catalog\Model\Category;
use Magento\Framework\Escaper;

/**
 * Class OptionsPreparer
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Category
 */
class OptionsPreparer
{
    /**
     * @var CategoryResolver
     */
    private $categoryResolver;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var SeoChecker
     */
    private $seoChecker;

    /**
     * @param CategoryResolver $categoryResolver
     * @param Escaper $escaper
     * @param SeoChecker $seoChecker
     */
    public function __construct(
        CategoryResolver $categoryResolver,
        Escaper $escaper,
        SeoChecker $seoChecker
    ) {
        $this->categoryResolver = $categoryResolver;
        $this->escaper = $escaper;
        $this->seoChecker = $seoChecker;
    }

    /**
     * Perform option preparation
     *
     * @param Category $category
     * @param array $optionCounts
     * @return array
     */
    public function perform($category, $optionCounts)
    {
        $childCategories = $category->getChildrenCategories();
        $preparedOptions = [];
        foreach ($childCategories as $childCategory) {
            if ($childCategory->getIsActive() && isset($optionCounts[$childCategory->getId()])) {
                $optionValue = $childCategory->getId();
                if ($this->seoChecker->isNeedToUseTextValues()) {
                    $categoryIds = explode(',', $optionValue);
                    $urlKeys = $this->categoryResolver->getCategoryUrlKeys($categoryIds);
                    $optionValue = implode(',', $urlKeys);
                }
                $preparedOptions[] = [
                    'label' => $this->escaper->escapeHtml($childCategory->getName()),
                    'value' => $optionValue,
                    'count' => $optionCounts[$childCategory->getId()]['count']
                ];
            }
        }

        return $preparedOptions;
    }
}
