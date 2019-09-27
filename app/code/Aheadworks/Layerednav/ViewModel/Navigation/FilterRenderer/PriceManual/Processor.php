<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\PriceManual;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\Resolver as FilterItemResolver;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Processor
 *
 * @package Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\PriceManual
 */
class Processor
{
    /**
     * Min value
     */
    const MIN_VALUE = 0.01;

    /**
     * @var FilterItemResolver
     */
    private $filterItemResolver;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param FilterItemResolver $filterItemResolver
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        FilterItemResolver $filterItemResolver,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->filterItemResolver = $filterItemResolver;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Process min price data in the price data array
     *
     * @param $priceData
     * @return mixed
     */
    public function processMinPrice($priceData)
    {
        $priceData['minPrice'] = floor(
            $this->priceCurrency->convertAndRound(
                isset($priceData['minPrice']) ? $priceData['minPrice'] : 0
            )
        );
        return $priceData;
    }

    /**
     * Process max price data in the price data array
     *
     * @param $priceData
     * @return mixed
     */
    public function processMaxPrice($priceData)
    {
        $priceData['maxPrice'] = ceil(
            $this->priceCurrency->convertAndRound(
                isset($priceData['maxPrice']) ? $priceData['maxPrice'] : 0
            )
        );
        return $priceData;
    }

    /**
     * Add from-to price range from value of active filter item
     *
     * @param array $priceData
     * @param FilterInterface $filter
     * @return array
     */
    public function addPriceDataFromActiveFilterItem($priceData, $filter)
    {
        $activeFilterItem = $this->filterItemResolver->getActiveItemByFilter($filter);
        if ($activeFilterItem) {
            $priceData['fromPrice'] = $this->filterItemResolver->getPriceFromValue($activeFilterItem);
            $priceData['toPrice'] = $this->filterItemResolver->getPriceToValue($activeFilterItem);
        }
        return $priceData;
    }

    /**
     * Adjust range price data when price filter is inactive
     *
     * @param array $priceData
     * @return array
     */
    public function adjustRangeDataForInactivePriceFilter($priceData)
    {
        $priceData['fromPrice'] = $priceData['minPrice'] = floor(
            $this->priceCurrency->convertAndRound(
                isset($priceData['minSelectionPrice']) ? $priceData['minSelectionPrice'] : 0
            )
        );
        $priceData['toPrice'] = $priceData['maxPrice'] = ceil(
            $this->priceCurrency->convertAndRound(
                isset($priceData['maxSelectionPrice']) ? $priceData['maxSelectionPrice'] : 0
            )
        );
        return $priceData;
    }

    /**
     * @param array $priceData
     * @return array
     */
    public function adjustMinMaxPriceDataForActiveNotPriceFilter($priceData)
    {
        if ((isset($priceData['minSelectionPrice']) ? $priceData['minSelectionPrice'] : 0)
            > (isset($priceData['fromPrice']) ? $priceData['fromPrice'] : 0)
        ) {
            $priceData['minPrice'] = floor(
                $this->priceCurrency->convertAndRound(
                    (isset($priceData['fromPrice']) ? $priceData['fromPrice'] : 0) - self::MIN_VALUE
                )
            );
            $priceData['minPrice'] = $priceData['minPrice'] < 0 ? 0 : $priceData['minPrice'];
        }
        if ((isset($priceData['maxSelectionPrice']) ? $priceData['maxSelectionPrice'] : 0)
            < (isset($priceData['toPrice']) ? $priceData['toPrice'] : 0)
        ) {
            $priceData['maxPrice'] = ceil(
                $this->priceCurrency->convertAndRound(
                    (isset($priceData['toPrice']) ? $priceData['toPrice'] : 0) + self::MIN_VALUE
                )
            );
        }
        return $priceData;
    }
}
