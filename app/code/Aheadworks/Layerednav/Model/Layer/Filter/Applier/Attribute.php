<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Applier;

use Aheadworks\Layerednav\Model\Layer\Filter\Applier\Attribute\ValueResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemListBuilder as FilterItemListBuilder;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Layer\State\Applier as LayerStateApplier;

/**
 * Class Attribute
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Applier
 */
class Attribute implements ApplierInterface
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
     * @var ValueResolver
     */
    private $valueResolver;

    /**
     * @param LayerStateApplier $layerStateApplier
     * @param FilterItemListBuilder $itemListBuilder
     * @param ValueResolver $valueResolver
     */
    public function __construct(
        LayerStateApplier $layerStateApplier,
        FilterItemListBuilder $itemListBuilder,
        ValueResolver $valueResolver
    ) {
        $this->layerStateApplier = $layerStateApplier;
        $this->itemListBuilder = $itemListBuilder;
        $this->valueResolver = $valueResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($request, $filter)
    {
        $filterData = $request->getParam($filter->getCode());
        if ($filterData === null || !is_string($filterData)) {
            return $this;
        }
        $filterParams = explode(',', $filterData);
        $attributeCode = $filter->getAttributeModel()->getAttributeCode();

        foreach ($filterParams as $filterParam) {
            $label = $this->getOptionText($filter, $filterParam);
            $value = $this->valueResolver->getValue($label, $filterParam);
            $this->itemListBuilder->add($filter, $label, $value, 0);
        }
        $this->layerStateApplier->add($this->itemListBuilder->create(), $attributeCode, $filterParams, true);

        return $this;
    }

    /**
     * Get option text from frontend model by option id
     *
     * @param FilterInterface $filter
     * @param int $optionId
     * @return string|bool
     */
    private function getOptionText($filter, $optionId)
    {
        return $filter->getAttributeModel()
            ->getFrontend()
            ->getOption($optionId);
    }
}
