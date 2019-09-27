<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider;

use Aheadworks\Layerednav\Model\Layer\Filter\DataResolver as FilterDataResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProviderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer\Range as RangePreparer;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;

/**
 * Class Price
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider
 */
class Price implements DataProviderInterface
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
     * @var array
     */
    private $facets;

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
        $facets = $this->getFacets($filter);

        $itemsData = [];
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
     * Get facets
     *
     * @param FilterInterface $filter
     * @return array
     * @throws \Magento\Framework\Exception\StateException
     */
    private function getFacets($filter)
    {
        if ($this->facets === null) {
            $this->facets = $this->filterDataResolver->getFacetedData($filter);
        }

        return $this->facets;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatisticsData($filter)
    {
        $statisticsData = [];
        $facets = $this->getFacets($filter);
        $statisticsData = array_merge($statisticsData, $this->getMinMaxPriceData($facets));
        $step = $this->getStepOfFacetedData($facets);
        if ($step) {
            $statisticsData['step'] = $step;
        }
        return $statisticsData;
    }

    /**
     * Retrieve min and max price data
     *
     * @param array $facetsData
     * @return array
     */
    private function getMinMaxPriceData($facetsData)
    {
        $minMaxPrices = [];
        if (isset($facetsData['stats'])
            && isset($facetsData['stats']['min'])
            && isset($facetsData['stats']['max'])
        ) {
            $minMaxPrices = [
                'minPrice' => $facetsData['stats']['min'],
                'maxPrice' => $facetsData['stats']['max'],
                'minSelectionPrice' => $facetsData['stats']['min'],
                'maxSelectionPrice' => $facetsData['stats']['max'],
            ];
        }

        return $minMaxPrices;
    }

    /**
     * Retrieve step of
     *
     * @param array $facetsData
     * @return int|null
     */
    private function getStepOfFacetedData($facetsData)
    {
        $step = null;
        if (count($facetsData) > 0) {
            $firstFacet = reset($facetsData);
            if (isset($firstFacet['value'])) {
                $from = $this->rangePreparer->getFromValueByKey($firstFacet['value'], false);
                $to = $this->rangePreparer->getToValueByKey($firstFacet['value'], false);
                if ((!empty($from) || $from === 0 || $from === 0.0)
                    && (!empty($to) || $to === 0 || $to === 0.0)
                ) {
                    $step = abs($to - $from);
                }
            }
        }
        return $step;
    }
}
