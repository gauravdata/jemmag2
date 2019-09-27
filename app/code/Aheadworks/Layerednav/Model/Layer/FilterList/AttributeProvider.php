<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\FilterList;

use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Class AttributeProvider
 * @package Aheadworks\Layerednav\Model\Layer\FilterList
 */
class AttributeProvider implements AttributeProviderInterface
{
    /**
     * @var FilterableAttributeListInterface
     */
    private $filterableAttributes;

    /**
     * @param FilterableAttributeListInterface $filterableAttributes
     */
    public function __construct(
        FilterableAttributeListInterface $filterableAttributes
    ) {
        $this->filterableAttributes = $filterableAttributes;
    }

    /**
     * Get filterable attributes
     *
     * @return Attribute[] ["attribute_code" => Attribute, ...]
     */
    public function getAttributes()
    {
        $filterableAttributesList = $this->filterableAttributes->getList();
        if (is_array($filterableAttributesList)) {
            $filterableAttributes = $filterableAttributesList;
        } else {
            $filterableAttributes = $filterableAttributesList->getItems();
        }

        $attributes = [];
        /** @var Attribute $attribute */
        foreach ($filterableAttributes as $attribute) {
            $attributes[$attribute->getAttributeCode()] = $attribute;
        }

        return $attributes;
    }
}
