<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface FilterInterface
 * @package Aheadworks\Layerednav\Api\Data
 */
interface FilterInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID                        = 'id';
    const DEFAULT_TITLE             = 'default_title';
    const STOREFRONT_TITLE          = 'storefront_title';
    const STOREFRONT_TITLES         = 'storefront_titles';
    const CODE                      = 'code';
    const TYPE                      = 'type';
    const IS_FILTERABLE             = 'is_filterable';
    const IS_FILTERABLE_IN_SEARCH   = 'is_filterable_in_search';
    const POSITION                  = 'position';
    const STOREFRONT_DISPLAY_STATE  = 'storefront_display_state';
    const DISPLAY_STATES            = 'display_states';
    const STOREFRONT_SORT_ORDER     = 'storefront_sort_order';
    const SORT_ORDERS               = 'sort_orders';
    const CATEGORY_MODE             = 'category_mode';
    const EXCLUDE_CATEGORY_IDS      = 'exclude_category_ids';
    const IMAGE                     = 'image';
    const IMAGE_STOREFRONT_TITLE    = 'image_storefront_title';
    const IMAGE_TITLES              = 'image_titles';
    const SWATCHES_VIEW_MODE        = 'swatches_view_mode';
    /**#@-*/

    /**#@+
     * General filter types
     */
    const CATEGORY_FILTER   = 'category';
    const ATTRIBUTE_FILTER  = 'attribute';
    const PRICE_FILTER      = 'price';
    const DECIMAL_FILTER    = 'decimal';
    /**#@-*/

    /**#@+
     * Special filter types
     */
    const SALES_FILTER      = 'on-sale';
    const NEW_FILTER        = 'new';
    const STOCK_FILTER      = 'in-stock';
    /**#@-*/

    /**
     * Custom filter types
     */
    const CUSTOM_FILTER_TYPES = [
        'cat'       => self::CATEGORY_FILTER,
        'aw_sales'  => self::SALES_FILTER,
        'aw_new'    => self::NEW_FILTER,
        'aw_stock'  => self::STOCK_FILTER,
    ];

    /**
     * Attribute filter types
     */
    const ATTRIBUTE_FILTER_TYPES = [
        self::ATTRIBUTE_FILTER,
        self::PRICE_FILTER,
        self::DECIMAL_FILTER,
    ];

    /**#@+
     * Sort mode values
     */
    const SORT_ORDER_MANUAL = 'default';
    const SORT_ORDER_ASC    = 'label_asc';
    const SORT_ORDER_DESC   = 'label_desc';
    /**#@-*/

    /**#@+
     * Display on category mode values
     */
    const CATEGORY_MODE_ALL             = 1;
    const CATEGORY_MODE_LOWEST_LEVEL    = 2;
    const CATEGORY_MODE_EXCLUDE         = 3;
    /**#@-*/

    /**#@+
     * Display state values
     */
    const DISPLAY_STATE_EXPANDED    = 1;
    const DISPLAY_STATE_COLLAPSED   = 2;
    /**#@-*/

    /**
     * Get filter id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set filter id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get default title
     * @return string
     */
    public function getDefaultTitle();

    /**
     * Set default title
     *
     * @param string $defaultTitle
     * @return $this
     */
    public function setDefaultTitle($defaultTitle);

    /**
     * Get code
     * @return string
     */
    public function getCode();

    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * Get type
     * @return string
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get is filterable
     * @return string
     */
    public function getIsFilterable();

    /**
     * Set is filterable
     *
     * @param string $isFilterable
     * @return $this
     */
    public function setIsFilterable($isFilterable);

    /**
     * Get is filterable in search
     * @return string
     */
    public function getIsFilterableInSearch();

    /**
     * Set is filterable in search
     *
     * @param string $isFilterableInSearch
     * @return $this
     */
    public function setIsFilterableInSearch($isFilterableInSearch);

    /**
     * Get position
     * @return string
     */
    public function getPosition();

    /**
     * Set position
     *
     * @param string $position
     * @return $this
     */
    public function setPosition($position);

    /**
     * Get storefront title
     * @return string
     */
    public function getStorefrontTitle();

    /**
     * Set storefront title
     *
     * @param string $storefrontTitle
     * @return $this
     */
    public function setStorefrontTitle($storefrontTitle);

    /**
     * Get titles
     * @return \Aheadworks\Layerednav\Api\Data\StoreValueInterface[]
     */
    public function getStorefrontTitles();

    /**
     * Set titles
     *
     * @param \Aheadworks\Layerednav\Api\Data\StoreValueInterface[] $titles
     * @return $this
     */
    public function setStorefrontTitles($titles = []);

    /**
     * Get storefront display state
     * @return int
     */
    public function getStorefrontDisplayState();

    /**
     * Set storefront display state
     *
     * @param int $storefrontDisplayState
     * @return $this
     */
    public function setStorefrontDisplayState($storefrontDisplayState);

    /**
     * Get display states
     * @return \Aheadworks\Layerednav\Api\Data\StoreValueInterface[]
     */
    public function getDisplayStates();

    /**
     * Set display states
     *
     * @param \Aheadworks\Layerednav\Api\Data\StoreValueInterface[] $displayStates
     * @return $this
     */
    public function setDisplayStates($displayStates);

    /**
     * Get storefront sort order
     * @return string
     */
    public function getStorefrontSortOrder();

    /**
     * Set storefront sort order
     *
     * @param string $storefrontSortOrder
     * @return $this
     */
    public function setStorefrontSortOrder($storefrontSortOrder);

    /**
     * Get sort orders
     * @return \Aheadworks\Layerednav\Api\Data\StoreValueInterface[]|null
     */
    public function getSortOrders();

    /**
     * Set sort orders
     *
     * @param \Aheadworks\Layerednav\Api\Data\StoreValueInterface[]|null $sortOrders
     * @return $this
     */
    public function setSortOrders($sortOrders);

    /**
     * Get category mode
     * @return int
     */
    public function getCategoryMode();

    /**
     * Set category mode
     *
     * @param int $categoryMode
     * @return $this
     */
    public function setCategoryMode($categoryMode);

    /**
     * Get exclude category ids
     * @return int[]|null
     */
    public function getExcludeCategoryIds();

    /**
     * Set exclude category ids
     *
     * @param int[]|null $excludeCategoryIds
     * @return $this
     */
    public function setExcludeCategoryIds($excludeCategoryIds);

    /**
     * Get image
     *
     * @return \Aheadworks\Layerednav\Api\Data\ImageInterface|null
     */
    public function getImage();

    /**
     * Get image storefront title
     * @return string
     */
    public function getImageStorefrontTitle();

    /**
     * Set image storefront title
     *
     * @param string $imageStorefrontTitle
     * @return $this
     */
    public function setImageStorefrontTitle($imageStorefrontTitle);

    /**
     * Get image titles
     * @return \Aheadworks\Layerednav\Api\Data\StoreValueInterface[]
     */
    public function getImageTitles();

    /**
     * Set image titles
     *
     * @param \Aheadworks\Layerednav\Api\Data\StoreValueInterface[] $imageTitles
     * @return $this
     */
    public function setImageTitles($imageTitles = []);

    /**
     * Set image
     *
     * @param \Aheadworks\Layerednav\Api\Data\ImageInterface|null $image
     * @return $this
     */
    public function setImage($image);

    /**
     * Get swatches view mode
     *
     * @return int
     */
    public function getSwatchesViewMode();

    /**
     * Set swatches view mode
     *
     * @param int $swatchesViewMode
     * @return $this
     */
    public function setSwatchesViewMode($swatchesViewMode);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Layerednav\Api\Data\FilterExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Layerednav\Api\Data\FilterExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Layerednav\Api\Data\FilterExtensionInterface $extensionAttributes
    );
}
