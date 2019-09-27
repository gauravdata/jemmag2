<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Api\Data\Filter;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface SwatchInterface
 *
 * @package Aheadworks\Layerednav\Api\Data\Filter
 */
interface SwatchInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID                        = 'id';
    const FILTER_ID                 = 'filter_id';
    const IS_DEFAULT                = 'is_default';
    const SORT_ORDER                = 'sort_order';
    const VALUE                     = 'value';
    const IMAGE                     = 'image';
    const OPTION_ID                 = 'option_id';
    const CURRENT_STOREFRONT_TITLE  = 'current_storefront_title';
    const STOREFRONT_TITLES         = 'storefront_titles';
    /**#@-*/

    /**
     * Get swatch id
     *
     * @return int
     */
    public function getId();

    /**
     * Set swatch id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get filter id
     *
     * @return int
     */
    public function getFilterId();

    /**
     * Set filter id
     *
     * @param int $filterId
     * @return $this
     */
    public function setFilterId($filterId);

    /**
     * Get is default flag
     *
     * @return bool
     */
    public function getIsDefault();

    /**
     * Set is default flag
     *
     * @param bool $isDefault
     * @return $this
     */
    public function setIsDefault($isDefault);

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Get value
     *
     * @return string|null
     */
    public function getValue();

    /**
     * Set value
     *
     * @param string|null $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Get image
     *
     * @return \Aheadworks\Layerednav\Api\Data\ImageInterface|null
     */
    public function getImage();

    /**
     * Set image
     *
     * @param \Aheadworks\Layerednav\Api\Data\ImageInterface|null $image
     * @return $this
     */
    public function setImage($image);

    /**
     * Get option id
     *
     * @return int|null
     */
    public function getOptionId();

    /**
     * Set option id
     *
     * @param int $optionId
     * @return $this
     */
    public function setOptionId($optionId);

    /**
     * Get current storefront title
     * @return string
     */
    public function getCurrentStorefrontTitle();

    /**
     * Set current storefront title
     *
     * @param string $currentStorefrontTitle
     * @return $this
     */
    public function setCurrentStorefrontTitle($currentStorefrontTitle);

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
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Layerednav\Api\Data\Filter\SwatchExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Layerednav\Api\Data\Filter\SwatchExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Layerednav\Api\Data\Filter\SwatchExtensionInterface $extensionAttributes
    );
}
