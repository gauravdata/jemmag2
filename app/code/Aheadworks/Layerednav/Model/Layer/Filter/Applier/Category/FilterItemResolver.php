<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Applier\Category;

use Aheadworks\Layerednav\Model\Category\Resolver as CategoryResolver;
use Aheadworks\Layerednav\Model\Seo\Checker as SeoChecker;
use Magento\Framework\Escaper;

/**
 * Class FilterItemResolver
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Applier\Category
 */
class FilterItemResolver
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
     * Get label
     *
     * @param int $categoryId
     * @return string
     */
    public function getLabel($categoryId)
    {
        $name = $this->categoryResolver->getCategoryName($categoryId);
        return $this->escaper->escapeHtml($name);
    }

    /**
     * Get value
     *
     * @param int $categoryId
     * @return string
     */
    public function getValue($categoryId)
    {
        $value = (string)$categoryId;
        if ($this->seoChecker->isNeedToUseTextValues()) {
            $categoryIds = [$categoryId];
            $urlKeys = $this->categoryResolver->getCategoryUrlKeys($categoryIds);
            $value = implode(',', $urlKeys);
        }
        return $value;
    }
}
