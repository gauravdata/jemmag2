<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Magento\Framework\Locale\FormatInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Checker as FilterChecker;
use Aheadworks\Layerednav\Model\Layer\Checker as LayerChecker;
use Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\PriceManual\Processor as PriceSliderProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Magento\Directory\Model\Currency;
use Aheadworks\Layerednav\Model\Source\Filter\PriceSlider\BehaviourMode as PriceSliderBehaviourMode;

/**
 * Class PriceManual
 *
 * @package Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer
 */
class PriceManual implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var FormatInterface
     */
    private $localeFormat;

    /**
     * @var FilterChecker
     */
    private $filterChecker;

    /**
     * @var LayerChecker
     */
    private $layerChecker;

    /**
     * @var PriceSliderProcessor
     */
    private $priceSliderProcessor;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Config $config
     * @param FormatInterface $localeFormat
     * @param FilterChecker $filterChecker
     * @param LayerChecker $layerChecker
     * @param PriceSliderProcessor $priceSliderProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        FormatInterface $localeFormat,
        FilterChecker $filterChecker,
        LayerChecker $layerChecker,
        PriceSliderProcessor $priceSliderProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->localeFormat = $localeFormat;
        $this->filterChecker = $filterChecker;
        $this->layerChecker = $layerChecker;
        $this->priceSliderProcessor = $priceSliderProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve array of price data to display
     *
     * @param FilterInterface $filter
     * @return array
     */
    public function getPriceData($filter)
    {
        $priceData = $filter->getItemsProvider()->getStatisticsData($filter);
        $priceData = $this->priceSliderProcessor->processMinPrice($priceData);
        $priceData = $this->priceSliderProcessor->processMaxPrice($priceData);
        $priceData = $this->priceSliderProcessor->addPriceDataFromActiveFilterItem($priceData, $filter);
        if (!$this->filterChecker->isActive($filter)) {
            $priceData = $this->priceSliderProcessor->adjustRangeDataForInactivePriceFilter($priceData);
        } elseif ($this->layerChecker->hasActiveFilters()) {
            $priceData = $this->priceSliderProcessor->adjustMinMaxPriceDataForActiveNotPriceFilter($priceData);
        }
        return $priceData;
    }

    /**
     * Retrieve min price from price data array
     *
     * @param $priceData
     * @return float
     */
    public function getMinPrice($priceData)
    {
        return isset($priceData['minPrice']) ? $priceData['minPrice'] : 0;
    }

    /**
     * Retrieve max price from price data array
     *
     * @param $priceData
     * @return float
     */
    public function getMaxPrice($priceData)
    {
        return isset($priceData['maxPrice']) ? $priceData['maxPrice'] : 0;
    }

    /**
     * Retrieve from price from price data array
     *
     * @param $priceData
     * @return float
     */
    public function getFromPrice($priceData)
    {
        return isset($priceData['fromPrice']) ? $priceData['fromPrice'] : 0;
    }

    /**
     * Retrieve to price from price data array
     *
     * @param $priceData
     * @return float
     */
    public function getToPrice($priceData)
    {
        return isset($priceData['toPrice']) ? $priceData['toPrice'] : 0;
    }

    /**
     * Retrieve array for js price formatting
     *
     * @return array
     */
    public function getPriceFormat()
    {
        return $this->localeFormat->getPriceFormat();
    }

    /**
     * Is price slider enabled.
     *
     * @return bool
     */
    public function isPriceSliderEnabled()
    {
        return $this->config->isPriceSliderEnabled();
    }

    /**
     * Are from-to inputs enabled
     *
     * @return bool
     */
    public function areFromToInputsEnabled()
    {
        return $this->config->isPriceFromToEnabled();
    }

    /**
     * Check if need to display filter label
     *
     * @return bool
     */
    public function isFilterLabelEnabled()
    {
        return $this->isPriceSliderEnabled() && !$this->areFromToInputsEnabled();
    }

    /**
     * Is filter button disabled.
     * If popover is enabled, price filter updates immediately on change
     *
     * @return bool
     */
    public function isFilterButtonDisabled()
    {
        return $this->config->isAjaxEnabled() && !$this->config->isPopoverDisabled();
    }

    /**
     * Get currency symbol
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        $currentCurrency = $this->getCurrency();
        if ($currentCurrency) {
            return $currentCurrency->getCurrencySymbol();
        } else {
            return '';
        }
    }

    /**
     * Check if currency symbol should be displayed
     *
     * @return bool
     */
    public function isNeedToDisplayCurrencySymbol()
    {
        $currentCurrency = $this->getCurrency();
        if ($currentCurrency) {
            $format = $currentCurrency->getOutputFormat();
            $currencySymbol = $this->getCurrencySymbol();
            $currencySymbolPlaceholderPosition = iconv_strpos($format, $currencySymbol);
            return $currencySymbolPlaceholderPosition !== false;
        } else {
            return false;
        }
    }

    /**
     * Check if currency symbol should be displayed after value
     *
     * @return bool
     */
    public function isNeedToDisplayCurrencySymbolAfterValue()
    {
        $currentCurrency = $this->getCurrency();
        if ($currentCurrency) {
            $format = $currentCurrency->getOutputFormat();
            $currencySymbol = $this->getCurrencySymbol();
            $currencySymbolPlaceholderPosition = iconv_strpos($format, $currencySymbol);
            return $currencySymbolPlaceholderPosition > 0;
        } else {
            return false;
        }
    }

    /**
     * Check if need to set discrete step for the price slider
     *
     * @return bool
     */
    public function isNeedToSetDiscreteStepForSlider()
    {
        return $this->config->getPriceSliderBehaviourMode() == PriceSliderBehaviourMode::DISCRETE;
    }

    /**
     * Retrieve value of step for the price slider
     *
     * @param array $priceData
     * @return int
     */
    public function getStepForSlider($priceData)
    {
        return isset($priceData['step']) ? $priceData['step'] : 1;
    }

    /**
     * Get currency of current store
     *
     * @return Currency|null
     */
    private function getCurrency()
    {
        try {
            /** @var Store $currentStore */
            $currentStore = $this->storeManager->getStore();
            $currency = $currentStore->getCurrentCurrency();
            return $currency;
        } catch (LocalizedException $exception) {
            return null;
        }
    }
}
