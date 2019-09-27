<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldName\CustomResolver;

use Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldName\CustomResolverInterface;
use Magento\Customer\Api\Data\GroupInterface;

/**
 * Class Sales
 * @package Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldName\CustomResolver
 */
class Sales implements CustomResolverInterface
{
    /**
     * Get field name
     *
     * @param string $attributeCode
     * @param array $context
     * @return string
     */
    public function getFieldName($attributeCode, $context = [])
    {
        $websiteId = isset($context['website_id']) ? $context['website_id'] : 0;
        $customerGroupId = isset($context['customer_group_id'])
            ? $context['customer_group_id']
            : GroupInterface::NOT_LOGGED_IN_ID;

        return $attributeCode . '_' . $customerGroupId . '_' . $websiteId;
    }
}
