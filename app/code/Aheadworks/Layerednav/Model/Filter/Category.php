<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter;

use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class Category
 * @package Aheadworks\Layerednav\Model\Filter
 */
class Category extends AbstractExtensibleObject implements FilterCategoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getStorefrontListStyle()
    {
        return $this->_get(self::STOREFRONT_LIST_STYLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStorefrontListStyle($storefrontListStyle)
    {
        return $this->setData(self::STOREFRONT_LIST_STYLE, $storefrontListStyle);
    }

    /**
     * {@inheritdoc}
     */
    public function getListStyles()
    {
        return $this->_get(self::LIST_STYLES);
    }

    /**
     * {@inheritdoc}
     */
    public function setListStyles($listStyles)
    {
        return $this->setData(self::LIST_STYLES, $listStyles);
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
        \Aheadworks\Layerednav\Api\Data\FilterCategoryExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
