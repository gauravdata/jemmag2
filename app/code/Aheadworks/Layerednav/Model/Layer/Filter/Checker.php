<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter;

use Aheadworks\Layerednav\Api\Data\FilterInterface as FilterDataInterface;
use Aheadworks\Layerednav\Api\Data\Filter\ModeInterface as FilterModeDataInterface;
use Aheadworks\Layerednav\Model\Layer\FilterInterface as LayerFilterInterface;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Layer\State as LayerState;
use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface as FilterCategoryDataInterface;
use Magento\Swatches\Helper\Data as SwatchesHelper;
use Aheadworks\Layerednav\Model\Filter\Checker as FilterChecker;

/**
 * Class Checker
 *
 * @package Aheadworks\Layerednav\Model\Layer\Filter
 */
class Checker
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var LayerState
     */
    private $layerState;

    /**
     * @var SwatchesHelper
     */
    private $swatchesHelper;

    /**
     * @var FilterChecker
     */
    private $filterChecker;

    /**
     * @param Config $config
     * @param LayerState $layerState
     * @param SwatchesHelper $swatchesHelper
     * @param FilterChecker $filterChecker
     */
    public function __construct(
        Config $config,
        LayerState $layerState,
        SwatchesHelper $swatchesHelper,
        FilterChecker $filterChecker
    ) {
        $this->config = $config;
        $this->layerState = $layerState;
        $this->swatchesHelper = $swatchesHelper;
        $this->filterChecker = $filterChecker;
    }

    /**
     * Check if multi select is available
     *
     * @param LayerFilterInterface $filter
     * @return bool
     */
    public function isMultiselectAvailable(LayerFilterInterface $filter)
    {
        return $filter->getAdditionalData(FilterModeDataInterface::STOREFRONT_FILTER_MODE)
            == FilterModeDataInterface::MODE_MULTI_SELECT;
    }

    /**
     * Can show filter
     *
     * @param LayerFilterInterface $filter
     * @return bool
     */
    public function isNeedToDisplay($filter)
    {
        $filterItems = $filter->getItems();
        if (empty($filterItems)) {
            return false;
        } elseif (!$this->config->hideEmptyFilters()) {
            return true;
        } else {
            foreach ($filterItems as $filterItem) {
                if ($filterItem->getCount()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if the filter is active
     *
     * @param LayerFilterInterface $filter
     * @return bool
     */
    public function isActive($filter)
    {
        $activeFilterItems = $this->layerState->getItems();
        if (!empty($activeFilterItems)) {
            foreach ($activeFilterItems as $activeItem) {
                if ($activeItem->getFilterItem()->getFilter()->getCode() == $filter->getCode()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if category filter is active
     *
     * @return bool
     */
    public function isCategoryFilterActive()
    {
        $activeFilterItems = $this->layerState->getItems();
        if (!empty($activeFilterItems)) {
            foreach ($activeFilterItems as $activeItem) {
                if ($this->isCategory($activeItem->getFilterItem()->getFilter())
                    && !empty($activeItem->getFilterItem()->getValue())
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if filter needs to be expanded
     *
     * @param LayerFilterInterface $filter
     * @return bool
     */
    public function isDisplayStateExpanded($filter)
    {
        return $filter->getAdditionalData(FilterDataInterface::STOREFRONT_DISPLAY_STATE)
            == FilterInterface::DISPLAY_STATE_EXPANDED;
    }

    /**
     * Check if filter applied to the category
     *
     * @param LayerFilterInterface $filter
     * @return bool
     */
    public function isCategory($filter)
    {
        return $filter->getCode() == 'cat';
    }

    /**
     * Check if for specific category filter, single path style is applied
     *
     * @param LayerFilterInterface $filter
     * @return bool
     */
    public function isSinglePathStyleAppliedForCategoryFilter($filter)
    {
        return $this->isCategory($filter)
            && $filter->getAdditionalData(FilterCategoryDataInterface::STOREFRONT_LIST_STYLE)
            == FilterCategoryDataInterface::CATEGORY_STYLE_SINGLE_PATH;
    }

    /**
     * Check if filter is related to the swatch attribute
     *
     * @param LayerFilterInterface $filter
     * @return bool
     */
    public function isSwatchAttribute($filter)
    {
        $isSwatchAttribute = false;
        $attributeModel = $filter->getAttributeModel();
        if ($attributeModel) {
            $isSwatchAttribute = $this->swatchesHelper->isSwatchAttribute($attributeModel);
        }
        return $isSwatchAttribute;
    }

    /**
     * Check if filter is related to the price attribute
     *
     * @param LayerFilterInterface $filter
     * @return bool
     */
    public function isPrice($filter)
    {
        $isPrice = false;
        $attributeModel = $filter->getAttributeModel();
        if ($attributeModel) {
            $isPrice = $attributeModel->getAttributeCode() == FilterInterface::PRICE_FILTER;
        }
        return $isPrice;
    }

    /**
     * Check if need to show filter item swatch image
     *
     * @param LayerFilterInterface $filter
     * @return bool
     */
    public function isNeedToShowFilterItemImage($filter)
    {
        $swatchesViewMode = $filter->getAdditionalData(FilterInterface::SWATCHES_VIEW_MODE);
        return $this->filterChecker->isNeedToShowSwatchImage($swatchesViewMode);
    }

    /**
     * Check if need to show filter item label
     *
     * @param LayerFilterInterface $filter
     * @return bool
     */
    public function isNeedToShowFilterItemLabel($filter)
    {
        $swatchesViewMode = $filter->getAdditionalData(FilterInterface::SWATCHES_VIEW_MODE);
        return $this->filterChecker->isNeedToShowSwatchTitle($swatchesViewMode);
    }
}
