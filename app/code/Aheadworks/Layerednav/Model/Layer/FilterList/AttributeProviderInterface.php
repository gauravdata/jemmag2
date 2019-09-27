<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\FilterList;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Interface AttributeProviderInterface
 * @package Aheadworks\Layerednav\Model\Layer\FilterList
 */
interface AttributeProviderInterface
{
    /**
     * Get filterable attributes
     *
     * @return Attribute[] ["attribute_code" => Attribute, ...]
     */
    public function getAttributes();
}
