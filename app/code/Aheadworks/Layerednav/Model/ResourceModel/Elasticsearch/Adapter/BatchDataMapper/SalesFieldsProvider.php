<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\BatchDataMapper;

use Aheadworks\Layerednav\Model\Customer\GroupResolver as CustomerGroupResolver;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Product\Collection\SalesProvider;
use Aheadworks\Layerednav\Model\Store\Resolver as StoreResolver;

/**
 * Class SalesFieldsProvider
 * @package Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\BatchDataMapper
 */
class SalesFieldsProvider implements AdditionalFieldsProviderInterface
{
    /**
     * Field name
     */
    const FIELD_NAME = 'aw_sales';

    /**
     * @var SalesProvider
     */
    private $provider;

    /**
     * @var StoreResolver
     */
    private $storeResolver;

    /**
     * @var CustomerGroupResolver
     */
    private $customerGroupResolver;

    /**
     * @var array
     */
    private $productIds;

    /**
     * @param SalesProvider $provider
     * @param StoreResolver $storeResolver
     * @param CustomerGroupResolver $customerGroupResolver
     */
    public function __construct(
        SalesProvider $provider,
        StoreResolver $storeResolver,
        CustomerGroupResolver $customerGroupResolver
    ) {
        $this->provider = $provider;
        $this->storeResolver = $storeResolver;
        $this->customerGroupResolver = $customerGroupResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields(array $productIds, $storeId)
    {
        $websiteId = $this->storeResolver->getWebsiteIdByStoreId($storeId);
        $customerGroupIds = $this->customerGroupResolver->getAllCustomerGroupIds();

        $fields = [];
        foreach ($productIds as $productId) {
            foreach ($customerGroupIds as $customerGroupId) {
                $fields[$productId][self::FIELD_NAME . '_' . $customerGroupId . '_'. $websiteId] =
                    $this->getOnSaleValue($productId, $customerGroupId, $storeId);
            }
        }

        return $fields;
    }

    /**
     * Retrieve on sale attribute value
     *
     * @param int $productId
     * @param int $customerGroupId
     * @param int $storeId
     * @return bool
     */
    private function getOnSaleValue($productId, $customerGroupId, $storeId)
    {
        return in_array($productId, $this->getOnSaleProductIds($customerGroupId, $storeId))
                ? FilterInterface::CUSTOM_FILTER_VALUE_YES
                : FilterInterface::CUSTOM_FILTER_VALUE_NO;
    }

    /**
     * Get on sale product ids
     *
     * @param int $customerGroupId
     * @param int $storeId
     * @return array
     */
    private function getOnSaleProductIds($customerGroupId, $storeId)
    {
        if (empty($this->productIds)
            || !isset($this->productIds[$storeId])
            || !isset($this->productIds[$storeId][$customerGroupId])
        ) {
            $this->productIds[$storeId][$customerGroupId] =
                $this->provider->getProductIds(true, $customerGroupId, $storeId);
        }

        return $this->productIds[$storeId][$customerGroupId];
    }
}
