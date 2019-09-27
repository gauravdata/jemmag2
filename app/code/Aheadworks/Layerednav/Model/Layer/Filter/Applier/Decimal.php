<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Applier;

use Aheadworks\Layerednav\Model\Layer\Filter\Interval\Resolver as IntervalResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemListBuilder as FilterItemListBuilder;
use Aheadworks\Layerednav\Model\Layer\State\Applier as LayerStateApplier;
use Magento\Catalog\Model\Layer\Filter\Price\Render as PriceRenderer;

/**
 * Class Decimal
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Applier
 */
class Decimal implements ApplierInterface
{
    /**
     * @var IntervalResolver
     */
    private $intervalResolver;

    /**
     * @var LayerStateApplier
     */
    private $layerStateApplier;

    /**
     * @var FilterItemListBuilder
     */
    private $itemListBuilder;

    /**
     * @var PriceRenderer
     */
    private $priceRenderer;

    /**
     * @param IntervalResolver $intervalResolver
     * @param LayerStateApplier $layerStateApplier
     * @param FilterItemListBuilder $itemListBuilder
     * @param PriceRenderer $priceRenderer
     */
    public function __construct(
        IntervalResolver $intervalResolver,
        LayerStateApplier $layerStateApplier,
        FilterItemListBuilder $itemListBuilder,
        PriceRenderer $priceRenderer
    ) {
        $this->intervalResolver = $intervalResolver;
        $this->layerStateApplier = $layerStateApplier;
        $this->itemListBuilder = $itemListBuilder;
        $this->priceRenderer = $priceRenderer;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($request, $filter)
    {
        /**
         * Filter data must be string: $fromPrice-$toPrice
         */
        $filterData = $request->getParam($filter->getCode());
        if ($filterData === null || !is_string($filterData)) {
            return $this;
        }

        $interval = $this->intervalResolver->getInterval($filterData);
        if (!$interval) {
            return $this;
        }

        $from = $interval->getFrom();
        $to = $interval->getTo();
        $value = (string)$interval;
        $attributeCode = $filter->getAttributeModel()->getAttributeCode();

        $label = $this->priceRenderer->renderRangeLabel(empty($from) ? 0 : $from, $to);
        $condition = ['from' => $from, 'to' => $to];
        $this->itemListBuilder->add($filter, $label, $value, 0);
        $this->layerStateApplier->add($this->itemListBuilder->create(), $attributeCode, $condition, true);

        return $this;
    }
}
