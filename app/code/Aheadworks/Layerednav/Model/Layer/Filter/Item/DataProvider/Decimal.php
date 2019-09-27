<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider;

use Aheadworks\Layerednav\Model\Layer\Filter\DataResolver as FilterDataResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProviderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer\Range as RangePreparer;

/**
 * Class Decimal
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider
 */
class Decimal implements DataProviderInterface
{
    /**
     * @var FilterDataResolver
     */
    private $filterDataResolver;

    /**
     * @var RangePreparer
     */
    private $rangePreparer;

    /**
     * @param FilterDataResolver $filterDataResolver
     * @param RangePreparer $rangePreparer
     */
    public function __construct(
        FilterDataResolver $filterDataResolver,
        RangePreparer $rangePreparer
    ) {
        $this->filterDataResolver = $filterDataResolver;
        $this->rangePreparer = $rangePreparer;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsData($filter)
    {
        $itemsData = [];
        $facets = $this->filterDataResolver->getFacetedData($filter);
        if (count($facets) > 0) {
            foreach ($facets as $aggregation) {
                $value = $aggregation['value'];
                $count = $aggregation['count'];
                if (strpos($value, '_') === false) {
                    continue;
                }
                $itemsData[] = $this->rangePreparer->prepareData($value, $count, false);
            }
        }

        return $itemsData;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatisticsData($filter)
    {
        return [];
    }
}
