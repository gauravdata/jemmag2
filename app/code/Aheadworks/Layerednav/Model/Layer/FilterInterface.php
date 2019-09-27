<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer;

use Aheadworks\Layerednav\Model\Image\ViewInterface as ImageViewInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\ProviderInterface as ItemsProviderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Interface FilterInterface
 * @package Aheadworks\Layerednav\Model\Layer
 */
interface FilterInterface
{
    /**#@+
     * Custom filter value constants
     */
    const CUSTOM_FILTER_VALUE_YES = 1;
    const CUSTOM_FILTER_VALUE_NO = 0;
    /**#@-*/

    /**
     * Get code
     *
     * @return string
     */
    public function getCode();

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Get image
     *
     * @return ImageViewInterface|null
     */
    public function getImage();

    /**
     * Get filter items
     *
     * @return FilterItemInterface[]
     */
    public function getItems();

    /**
     * Get filter items count
     *
     * @return int
     */
    public function getItemsCount();

    /**
     * Get layer
     *
     * @return Layer
     */
    public function getLayer();

    /**
     * Get attribute model associated with filter
     *
     * @return Attribute|null
     */
    public function getAttributeModel();

    /**
     * Get data provider associated with filter
     *
     * @return ItemsProviderInterface
     */
    public function getItemsProvider();

    /**
     * Get additional data
     *
     * @param string $name
     * @return mixed|null
     */
    public function getAdditionalData($name);
}
