<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface FilterCategoryInterface
 * @package Aheadworks\Layerednav\Api\Data
 */
interface FilterCategoryInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const STOREFRONT_LIST_STYLE = 'storefront_list_style';
    const LIST_STYLES           = 'list_styles';
    /**#@-*/

    /**#@+
     * Category list style values
     */
    const CATEGORY_STYLE_DEFAULT        = 'default';
    const CATEGORY_STYLE_SINGLE_PATH    = 'single_path';
    /**#@-*/

    /**
     * Param name
     */
    const LIST_PARAM_NAME = 'list_style';

    /**
     * Get storefront list style
     * @return string
     */
    public function getStorefrontListStyle();

    /**
     * Set storefront list style
     *
     * @param string $storefrontListStyle
     * @return $this
     */
    public function setStorefrontListStyle($storefrontListStyle);

    /**
     * Get list styles
     * @return \Aheadworks\Layerednav\Api\Data\StoreValueInterface[]|null
     */
    public function getListStyles();

    /**
     * Set list styles
     *
     * @param \Aheadworks\Layerednav\Api\Data\StoreValueInterface[]|null $listStyles
     * @return $this
     */
    public function setListStyles($listStyles);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Layerednav\Api\Data\FilterCategoryExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Layerednav\Api\Data\FilterCategoryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Layerednav\Api\Data\FilterCategoryExtensionInterface $extensionAttributes
    );
}
