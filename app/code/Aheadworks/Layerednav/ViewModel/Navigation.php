<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Layer\DataSource\CompositeConfigProvider;
use Aheadworks\Layerednav\Model\Layer\FilterListAbstract;
use Aheadworks\Layerednav\Model\Layer\FilterListResolver;
use Aheadworks\Layerednav\Model\PageTypeResolver;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Aheadworks\Layerednav\Model\Layer\FilterInterface as LayerFilterInterface;
use Aheadworks\Layerednav\Model\Layer\Checker as LayerChecker;
use Aheadworks\Layerednav\Model\Layer\Filter\Checker as FilterChecker;
use Aheadworks\Layerednav\Model\Seo\Checker as SeoChecker;
use Aheadworks\Layerednav\Api\Data\FilterInterface;

/**
 * Class Navigation
 *
 * @package Aheadworks\Layerednav\ViewModel
 */
class Navigation implements ArgumentInterface
{
    /**
     * @var FilterListAbstract
     */
    private $filterList;

    /**
     * @var PageTypeResolver
     */
    private $pageTypeResolver;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CompositeConfigProvider
     */
    private $dataSourceConfigProvider;

    /**
     * @var LayerChecker
     */
    private $layerChecker;

    /**
     * @var LayerResolver
     */
    private $layerResolver;

    /**
     * @var FilterChecker
     */
    private $filterChecker;

    /**
     * @var SeoChecker
     */
    private $seoChecker;

    /**
     * @param FilterListResolver $filterListResolver
     * @param PageTypeResolver $pageTypeResolver
     * @param Config $config
     * @param CompositeConfigProvider $dataSourceConfigProvider
     * @param LayerChecker $layerChecker
     * @param LayerResolver $layerResolver
     * @param FilterChecker $filterChecker
     * @param SeoChecker $seoChecker
     */
    public function __construct(
        FilterListResolver $filterListResolver,
        PageTypeResolver $pageTypeResolver,
        Config $config,
        CompositeConfigProvider $dataSourceConfigProvider,
        LayerChecker $layerChecker,
        LayerResolver $layerResolver,
        FilterChecker $filterChecker,
        SeoChecker $seoChecker
    ) {
        $this->filterList = $filterListResolver->get();
        $this->pageTypeResolver = $pageTypeResolver;
        $this->config = $config;
        $this->dataSourceConfigProvider = $dataSourceConfigProvider;
        $this->layerChecker = $layerChecker;
        $this->layerResolver = $layerResolver;
        $this->filterChecker = $filterChecker;
        $this->seoChecker = $seoChecker;
    }

    /**
     * Check if need to render block
     *
     * @return bool
     */
    public function isNeedToRender()
    {
        return $this->layerChecker->isNavigationAvailable($this->getFilters());
    }

    /**
     * Get filters
     *
     * @return LayerFilterInterface[]
     */
    public function getFilters()
    {
        return $this->filterList->getFilters($this->layerResolver->get());
    }

    /**
     * Check if block has active filters
     *
     * @return bool
     */
    public function hasActiveFilters()
    {
        return $this->layerChecker->hasActiveFilters();
    }

    /**
     * Check if AJAX enabled on storefront
     *
     * @return bool
     */
    public function isAjaxEnabled()
    {
        return $this->config->isAjaxEnabled();
    }

    /**
     * Get data source config
     *
     * @return array
     */
    public function getDataSourceConfig()
    {
        return $this->dataSourceConfigProvider->getConfig();
    }

    /**
     * Check if "Show X Items" Pop-over disabled
     *
     * @return bool
     */
    public function isPopoverDisabled()
    {
        return $this->config->isPopoverDisabled();
    }

    /**
     * Check if need to use attribute value instead of Id in url build logic
     *
     * @return bool
     */
    public function isNeedToUseUseAttributeValueInsteadOfId()
    {
        return $this->seoChecker->isNeedToUseUseAttributeValueInsteadOfId();
    }

    /**
     * Check if need to use attribute values as subcategories in url build logic
     *
     * @return bool
     */
    public function isNeedToUseSubcategoriesAsAttributeValues()
    {
        return $this->seoChecker->isNeedToUseSubcategoriesAsAttributeValues();
    }

    /**
     * Check is need to display specific filter
     *
     * @param LayerFilterInterface $filter
     * @return bool
     */
    public function isNeedToDisplayFilter($filter)
    {
        return $this->filterChecker->isNeedToDisplay($filter);
    }

    /**
     * Check if need to expand specific filter
     *
     * @param LayerFilterInterface $filter
     * @param string $pageLayout
     * @return bool
     */
    public function isNeedToExpandFilter($filter, $pageLayout)
    {
        return (!$this->pageTypeResolver->isOneColumnLayoutApplied($pageLayout))
            && ($this->filterChecker->isDisplayStateExpanded($filter));
    }

    /**
     * Check if the filter is active
     *
     * @param LayerFilterInterface $filter
     * @return bool
     */
    public function isFilterActive($filter)
    {
        return $this->filterChecker->isActive($filter);
    }

    /**
     * Retrieve image title for filter
     *
     * @param LayerFilterInterface $filter
     * @return string
     */
    public function getImageTitle($filter)
    {
        return $filter->getAdditionalData(FilterInterface::IMAGE_STOREFRONT_TITLE);
    }
}
