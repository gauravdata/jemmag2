<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ImageInterface
 *
 * @package Aheadworks\Layerednav\Api\Data
 */
interface ImageInterface extends FileInterface, ExtensibleDataInterface
{
    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Layerednav\Api\Data\ImageExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Layerednav\Api\Data\ImageExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Layerednav\Api\Data\ImageExtensionInterface $extensionAttributes
    );
}
