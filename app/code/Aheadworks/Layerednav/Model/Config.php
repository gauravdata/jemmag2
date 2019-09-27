<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Config\Model\Config\Backend\Admin\Custom as BackendAdminCustom;

/**
 * Layered Navigation config
 */
class Config
{
    /**
     * Configuration path to display 'New' filter flag
     */
    const XML_PATH_NEW_FILTER_ENABLED = 'aw_layerednav/general/display_new';

    /**
     * Configuration path to display 'On Sale' filter flag
     */
    const XML_PATH_ON_SALE_FILTER_ENABLED = 'aw_layerednav/general/display_sales';

    /**
     * Configuration path to display 'In Stock' filter flag
     */
    const XML_PATH_STOCK_FILTER_ENABLED = 'aw_layerednav/general/display_stock';

    /**
     * Configuration path to Enable AJAX flag
     */
    const XML_PATH_AJAX_ENABLED = 'aw_layerednav/general/enable_ajax';

    /**
     * Configuration path to Enable AJAX flag
     */
    const XML_PATH_POPOVER_DISABLED = 'aw_layerednav/general/disable_popover';

    /**
     * Configuration path to display product count native option
     */
    const XML_PATH_DISPLAY_PRODUCT_COUNT = 'catalog/layered_navigation/display_product_count';

    /**
     * Configuration path to enable price slider
     */
    const XML_PATH_PRICE_SLIDER_ENABLED = 'aw_layerednav/general/enable_price_slider';

    /**
     * Configuration path to specify price slider behaviour
     */
    const XML_PATH_PRICE_SLIDER_BEHAVIOUR_MODE = 'aw_layerednav/general/price_slider_behaviour_mode';

    /**
     * Configuration path to enable price from-to inputs
     */
    const XML_PATH_PRICE_FROM_TO_ENABLED = 'aw_layerednav/general/enable_price_from_to_inputs';

    /**
     * Configuration path to filter mode
     */
    const XML_PATH_FILTER_MODE = 'aw_layerednav/general/filter_mode';

    /**
     * Configuration path to default filter state
     */
    const XML_PATH_FILTER_DISPLAY_STATE = 'aw_layerednav/general/filter_display_state';

    /**
     * Configuration path to filter values display limit
     */
    const XML_PATH_FILTER_VALUES_DISPLAY_LIMIT = 'aw_layerednav/general/filter_values_display_limit';

    /**
     * Configuration path to hide empty filters
     */
    const XML_PATH_HIDE_EMPTY_FILTERS = 'aw_layerednav/general/hide_empty_filters';

    /**
     * Configuration path to hide empty filters
     */
    const XML_PATH_HIDE_EMPTY_ATTRIBUTE_VALUES = 'aw_layerednav/general/hide_empty_attribute_values';

    /**
     * Configuration path to seo friendly url option
     */
    const XML_PATH_SEO_FRIENDLY_URL_OPTION = 'aw_layerednav/seo/seo_friendly_url';

    /**
     * Configuration path to redirect 301 for old urls flag
     */
    const XML_PATH_SEO_REDIRECT_FOR_OLD_URLS = 'aw_layerednav/seo/redirect_for_old_urls';

    /**
     * Configuration path to disable indexing on catalog search pages
     */
    const XML_PATH_SEO_DISABLE_INDEXING_ON_CATALOG_SEARCH= 'aw_layerednav/seo/disable_indexing_on_catalog_search';

    /**
     * Configuration path to page meta title template
     */
    const XML_PATH_SEO_PAGE_META_TITLE_TEMPLATE = 'aw_layerednav/seo/page_meta_title_template';

    /**
     * Configuration path to page meta description template
     */
    const XML_PATH_SEO_PAGE_META_DESCRIPTION_TEMPLATE = 'aw_layerednav/seo/page_meta_description_template';

    /**
     * Configuration path to rewrite meta robots tag flag
     */
    const XML_PATH_SEO_REWRITE_META_ROBOTS_TAG = 'aw_layerednav/seo/rewrite_meta_robots_tag';

    /**
     * Configuration path to add canonical urls flag
     */
    const XML_PATH_SEO_ADD_CANONICAL_URLS = 'aw_layerednav/seo/add_canonical_urls';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if 'New' filter enabled
     *
     * @return bool
     */
    public function isNewFilterEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_NEW_FILTER_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if 'On Sale' filter enabled
     *
     * @return bool
     */
    public function isOnSaleFilterEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ON_SALE_FILTER_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if 'In Stock' filter enabled
     *
     * @return bool
     */
    public function isInStockFilterEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_STOCK_FILTER_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if AJAX on storefront enabled
     *
     * @return bool
     */
    public function isAjaxEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_AJAX_ENABLED);
    }

    /**
     * Check if "Show X Items" Pop-over disabled
     *
     * @return bool
     */
    public function isPopoverDisabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_POPOVER_DISABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if need to show products count in the parentheses
     *
     * @return bool
     */
    public function isNeedToShowProductsCount()
    {
        $isDisplayProductCount = $this->scopeConfig->isSetFlag(
            self::XML_PATH_DISPLAY_PRODUCT_COUNT,
            ScopeInterface::SCOPE_STORE
        );
        return $isDisplayProductCount && $this->isPopoverDisabled();
    }

    /**
     * Check if price slider enabled
     *
     * @return bool
     */
    public function isPriceSliderEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_PRICE_SLIDER_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get price slider behaviour mode
     *
     * @return string
     */
    public function getPriceSliderBehaviourMode()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PRICE_SLIDER_BEHAVIOUR_MODE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if price from-to inputs enabled
     *
     * @return bool
     */
    public function isPriceFromToEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_PRICE_FROM_TO_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if manual price from-to filter enabled
     *
     * @return bool
     */
    public function isManualFromToPriceFilterEnabled()
    {
        return $this->isPriceSliderEnabled() || $this->isPriceFromToEnabled();
    }

    /**
     * Get filter mode
     *
     * @return string
     */
    public function getFilterMode()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FILTER_MODE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get filter display state
     *
     * @return int
     */
    public function getFilterDisplayState()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_FILTER_DISPLAY_STATE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get filter values display limit
     *
     * @return int
     */
    public function getFilterValuesDisplayLimit()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_FILTER_VALUES_DISPLAY_LIMIT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if empty filters should be hidden
     *
     * @return bool
     */
    public function hideEmptyFilters()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_HIDE_EMPTY_FILTERS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if empty attribute values should be hidden
     *
     * @return bool
     */
    public function hideEmptyAttributeValues()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_HIDE_EMPTY_ATTRIBUTE_VALUES,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get SEO friendly url option
     *
     * @return string
     */
    public function getSeoFriendlyUrlOption()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_FRIENDLY_URL_OPTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if search engine indexing is disabled on catalog search pages
     *
     * @return bool
     */
    public function isDisableIndexingOnCatalogSearch()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SEO_DISABLE_INDEXING_ON_CATALOG_SEARCH);
    }

    /**
     * Check if redirect from old urls enabled
     *
     * @return bool
     */
    public function isRedirectFromOldUrlsEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SEO_REDIRECT_FOR_OLD_URLS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get page meta title template
     *
     * @return string
     */
    public function getPageMetaTitleTemplate()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_PAGE_META_TITLE_TEMPLATE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get page meta description template
     *
     * @return string
     */
    public function getPageMetaDescriptionTemplate()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_PAGE_META_DESCRIPTION_TEMPLATE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if rewrite meta robots tag enabled
     *
     * @return bool
     */
    public function isRewriteMetaRobotsTagEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SEO_REWRITE_META_ROBOTS_TAG,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if add canonical urls enabled
     *
     * @return bool
     */
    public function isAddCanonicalUrlsEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SEO_ADD_CANONICAL_URLS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get search engine
     *
     * @return string
     */
    public function getSearchEngine()
    {
        return $this->scopeConfig->getValue(BackendAdminCustom::XML_PATH_CATALOG_SEARCH_ENGINE);
    }

    /**
     * Retrieve array of allowed file extensions for the filter image
     *
     * @return array
     */
    public function getAllowedExtensionsForFilterImage()
    {
        return [
            'jpg',
            'jpeg',
            'gif',
            'png',
        ];
    }
}
