<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Applier\Attribute;

use Aheadworks\Layerednav\Model\Seo\Checker as SeoChecker;
use Magento\Framework\Filter\FilterManager;

/**
 * Class ValuePreparer
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Applier\Attribute
 */
class ValueResolver
{
    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @var SeoChecker
     */
    private $seoChecker;

    /**
     * @param FilterManager $filterManager
     * @param SeoChecker $seoChecker
     */
    public function __construct(
        FilterManager $filterManager,
        SeoChecker $seoChecker
    ) {
        $this->filterManager = $filterManager;
        $this->seoChecker = $seoChecker;
    }

    /**
     * Get value
     *
     * @param string $label
     * @param int|string $value
     * @return int|string
     */
    public function getValue($label, $value)
    {
        $filterValue = $value;
        if ($this->seoChecker->isNeedToUseTextValues()) {
            $filterValue = $this->filterManager->translitUrl(urlencode($label));
        }
        return $filterValue;
    }
}
