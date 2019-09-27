<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter;

use Aheadworks\Layerednav\Api\Data\Filter\ModeInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class Mode
 * @package Aheadworks\Layerednav\Model\Filter
 */
class Mode extends AbstractExtensibleObject implements ModeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getStorefrontFilterMode()
    {
        return $this->_get(self::STOREFRONT_FILTER_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStorefrontFilterMode($mode)
    {
        return $this->setData(self::STOREFRONT_FILTER_MODE, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterModes()
    {
        return $this->_get(self::FILTER_MODES);
    }

    /**
     * {@inheritdoc}
     */
    public function setFilterModes($modes)
    {
        return $this->setData(self::FILTER_MODES, $modes);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\Layerednav\Api\Data\Filter\ModeExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
