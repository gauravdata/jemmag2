<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Applier;

use Aheadworks\Layerednav\Model\Category\Resolver as CategoryResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\Applier\Category\FilterItemResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemListBuilder as FilterItemListBuilder;
use Aheadworks\Layerednav\Model\Layer\State\Applier as LayerStateApplier;

/**
 * Class Category
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Applier
 */
class Category implements ApplierInterface
{
    /**
     * Category field name
     */
    const CATEGORY_FIELD_NAME = 'category_ids_query';

    /**
     * @var LayerStateApplier
     */
    private $layerStateApplier;

    /**
     * @var FilterItemListBuilder
     */
    private $itemListBuilder;

    /**
     * @var CategoryResolver
     */
    private $categoryResolver;

    /**
     * @var FilterItemResolver
     */
    private $filterItemResolver;

    /**
     * @param LayerStateApplier $layerStateApplier
     * @param FilterItemListBuilder $itemListBuilder
     * @param CategoryResolver $categoryResolver
     * @param FilterItemResolver $filterItemResolver
     */
    public function __construct(
        LayerStateApplier $layerStateApplier,
        FilterItemListBuilder $itemListBuilder,
        CategoryResolver $categoryResolver,
        FilterItemResolver $filterItemResolver
    ) {
        $this->layerStateApplier = $layerStateApplier;
        $this->itemListBuilder = $itemListBuilder;
        $this->categoryResolver = $categoryResolver;
        $this->filterItemResolver = $filterItemResolver;
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
        $storeId = $filter->getLayer()->getCurrentStore()->getId();
        $categoryIds = $this->categoryResolver->getActiveCategoryIds($filterParams, $storeId);
        if (!$categoryIds) {
            return $this;
        }

        foreach ($categoryIds as $categoryId) {
            $label = $this->filterItemResolver->getLabel($categoryId);
            $value = $this->filterItemResolver->getValue($categoryId);
            $this->itemListBuilder->add($filter, $label, $value, 0);
        }
        $this->layerStateApplier->add(
            $this->itemListBuilder->create(),
            self::CATEGORY_FIELD_NAME,
            $categoryIds,
            true
        );

        return $this;
    }
}
