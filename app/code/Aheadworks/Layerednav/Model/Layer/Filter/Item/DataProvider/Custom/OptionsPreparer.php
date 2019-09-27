<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Custom;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer\Option as OptionPreparer;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Seo\Checker as SeoChecker;
use Magento\Framework\Filter\FilterManager;

/**
 * Class OptionsPreparer
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Custom
 */
class OptionsPreparer
{
    /**
     * @var OptionPreparer
     */
    private $optionPreparer;

    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @var SeoChecker
     */
    private $seoChecker;

    /**
     * @param OptionPreparer $optionPreparer
     * @param FilterManager $filterManager
     * @param SeoChecker $seoChecker
     */
    public function __construct(
        OptionPreparer $optionPreparer,
        FilterManager $filterManager,
        SeoChecker $seoChecker
    ) {
        $this->optionPreparer = $optionPreparer;
        $this->filterManager = $filterManager;
        $this->seoChecker = $seoChecker;
    }

    /**
     * Perform option preparation
     *
     * @param FilterInterface $filter
     * @param array $options
     * @param array $optionCounts
     * @param bool $withCountOnly
     * @return array
     */
    public function perform($filter, $options, $optionCounts, $withCountOnly = true)
    {
        $preparedOptions = $this->optionPreparer->perform($options, $optionCounts, $withCountOnly);

        /** @var array $option */
        foreach ($preparedOptions as &$option) {
            $option['label'] = $this->filterManager->stripTags($option['label']);
            $option['value'] = $this->seoChecker->isNeedToUseTextValues()
                ? $this->getSeoFriendlyValue($filter)
                : $option['value'];
            $option['count'] = $option['count'];
            $option['image'] = isset($option['image']) ? $option['image'] : [];
        }

        return $preparedOptions;
    }

    /**
     * Get SEO friendly value
     *
     * @param FilterInterface $filter
     * @return string
     */
    private function getSeoFriendlyValue($filter)
    {
        return $filter->getType();
    }
}
