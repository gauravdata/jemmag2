<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Applier;

use Aheadworks\Layerednav\Model\Layer\Filter\ItemListBuilder as FilterItemListBuilder;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Layer\State\Applier as LayerStateApplier;
use Aheadworks\Layerednav\Model\Seo\Checker as SeoChecker;

/**
 * Class Custom
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Applier
 */
class Custom implements ApplierInterface
{
    /**
     * @var LayerStateApplier
     */
    private $layerStateApplier;

    /**
     * @var FilterItemListBuilder
     */
    private $itemListBuilder;

    /**
     * @var SeoChecker
     */
    private $seoChecker;

    /**
     * @param LayerStateApplier $layerStateApplier
     * @param FilterItemListBuilder $itemListBuilder
     * @param SeoChecker $seoChecker
     */
    public function __construct(
        LayerStateApplier $layerStateApplier,
        FilterItemListBuilder $itemListBuilder,
        SeoChecker $seoChecker
    ) {
        $this->layerStateApplier = $layerStateApplier;
        $this->itemListBuilder = $itemListBuilder;
        $this->seoChecker = $seoChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($request, $filter)
    {
        $filterData = $request->getParam($filter->getCode());
        if ($filterData === null || !is_numeric($filterData)) {
            return $this;
        }

        if ($filterData == FilterInterface::CUSTOM_FILTER_VALUE_YES) {
            $filterParams = [$filterData];
            $attributeCode = $filter->getCode();

            $label = $filter->getTitle();
            $value = $this->seoChecker->isNeedToUseTextValues()
                ? $this->getSeoFriendlyValue($filter)
                : $filterData;
            $this->itemListBuilder->add($filter, $label, $value, 0);
            $this->layerStateApplier->add($this->itemListBuilder->create(), $attributeCode, $filterParams, false);
        }

        return $this;
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
