<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider;

use Aheadworks\Layerednav\Model\Layer\Filter\DataResolver as FilterDataResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Category\OptionsPreparer;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProviderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderInterface;

/**
 * Class Category
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider
 */
class Category implements DataProviderInterface
{
    /**
     * Filter filed name
     */
    const FILTER_FIELD_NAME = 'category';

    /**
     * @var FilterDataResolver
     */
    private $filterDataResolver;

    /**
     * @var OptionsPreparer
     */
    private $optionsPreparer;

    /**
     * @var DataBuilderInterface
     */
    private $itemDataBuilder;

    /**
     * @param FilterDataResolver $filterDataResolver
     * @param OptionsPreparer $optionsPreparer
     * @param DataBuilderInterface $itemDataBuilder
     */
    public function __construct(
        FilterDataResolver $filterDataResolver,
        OptionsPreparer $optionsPreparer,
        DataBuilderInterface $itemDataBuilder
    ) {
        $this->filterDataResolver = $filterDataResolver;
        $this->optionsPreparer = $optionsPreparer;
        $this->itemDataBuilder = $itemDataBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsData($filter)
    {
        $category = $filter->getLayer()->getCurrentCategory();
        if ($category->getIsActive()) {
            $optionsFacetedData = $this->filterDataResolver->getFacetedData($filter, self::FILTER_FIELD_NAME);
            $preparedOptions = $this->optionsPreparer->perform($category, $optionsFacetedData);
            /** @var array $option */
            foreach ($preparedOptions as $option) {
                $this->itemDataBuilder->addItemData($option['label'], $option['value'], $option['count']);
            }
        }

        return $this->itemDataBuilder->build();
    }

    /**
     * {@inheritdoc}
     */
    public function getStatisticsData($filter)
    {
        return [];
    }
}
