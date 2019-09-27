<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Attribute;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer\Option as OptionPreparer;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer\Swatch as SwatchPreparer;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Product\Attribute\Checker as ProductAttributeChecker;
use Aheadworks\Layerednav\Model\Seo\Checker as SeoChecker;
use Magento\Framework\Filter\FilterManager;

/**
 * Class OptionsPreparer
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Attribute
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
     * @var SwatchPreparer
     */
    private $swatchPreparer;

    /**
     * @var ProductAttributeChecker
     */
    private $productAttributeChecker;

    /**
     * @param OptionPreparer $optionPreparer
     * @param FilterManager $filterManager
     * @param SeoChecker $seoChecker
     * @param SwatchPreparer $swatchPreparer
     * @param ProductAttributeChecker $productAttributeChecker
     */
    public function __construct(
        OptionPreparer $optionPreparer,
        FilterManager $filterManager,
        SeoChecker $seoChecker,
        SwatchPreparer $swatchPreparer,
        ProductAttributeChecker $productAttributeChecker
    ) {
        $this->optionPreparer = $optionPreparer;
        $this->filterManager = $filterManager;
        $this->seoChecker = $seoChecker;
        $this->swatchPreparer = $swatchPreparer;
        $this->productAttributeChecker = $productAttributeChecker;
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
        $attribute = $filter->getAttributeModel();
        $preparedOptions = $this->optionPreparer->perform($options, $optionCounts, $withCountOnly);
        if ($this->productAttributeChecker->areExtraSwatchesAllowed($attribute)) {
            $preparedOptions = $this->swatchPreparer->perform($preparedOptions);
        }

        /** @var array $option */
        foreach ($preparedOptions as &$option) {
            $option['label'] = $this->filterManager->stripTags($option['label']);
            $option['value'] = $this->seoChecker->isNeedToUseTextValues()
                ? $this->filterManager->translitUrl(urlencode($option['label']))
                : $option['value'];
            $option['count'] = $option['count'];
            $option['image'] = isset($option['image']) ? $option['image'] : [];
        }

        return $preparedOptions;
    }
}
