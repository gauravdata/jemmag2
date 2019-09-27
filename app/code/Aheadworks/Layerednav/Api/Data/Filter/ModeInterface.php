<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Api\Data\Filter;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Class ModeInterface
 * @package Aheadworks\Layerednav\Api\Data\Filter
 */
interface ModeInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const STOREFRONT_FILTER_MODE    = 'storefront_filter_mode';
    const FILTER_MODES              = 'filter_modes';
    /**#@-*/

    /**#@+
     * Mode values
     */
    const MODE_SINGLE_SELECT = 'single-select';
    const MODE_MULTI_SELECT  = 'multi-select';
    /**#@-*/

    /**
     * Get storefront filter mode
     * @return string|null
     */
    public function getStorefrontFilterMode();

    /**
     * Set storefront filter mode
     *
     * @param string $mode
     * @return $this
     */
    public function setStorefrontFilterMode($mode);

    /**
     * Get filter modes
     * @return \Aheadworks\Layerednav\Api\Data\StoreValueInterface[]
     */
    public function getFilterModes();

    /**
     * Set filter modes
     *
     * @param \Aheadworks\Layerednav\Api\Data\StoreValueInterface[] $modes
     * @return $this
     */
    public function setFilterModes($modes);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Layerednav\Api\Data\Filter\ModeExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Layerednav\Api\Data\Filter\ModeExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Layerednav\Api\Data\Filter\ModeExtensionInterface $extensionAttributes
    );
}
