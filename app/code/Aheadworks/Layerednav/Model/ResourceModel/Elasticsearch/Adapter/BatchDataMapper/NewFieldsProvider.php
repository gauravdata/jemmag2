<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\BatchDataMapper;

use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Product\Collection\NewProvider;

/**
 * Class NewFieldsProvider
 * @package Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\BatchDataMapper
 */
class NewFieldsProvider implements AdditionalFieldsProviderInterface
{
    /**
     * Field name
     */
    const FIELD_NAME = 'aw_new';

    /**
     * @var NewProvider
     */
    private $provider;

    /**
     * @var array
     */
    private $productIds;

    /**
     * @param NewProvider $provider
     */
    public function __construct(
        NewProvider $provider
    ) {
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields(array $productIds, $storeId)
    {
        $fields = [];
        foreach ($productIds as $productId) {
            $fields[$productId] = [
                self::FIELD_NAME => $this->isNew($productId, $storeId)
                    ? FilterInterface::CUSTOM_FILTER_VALUE_YES
                    : FilterInterface::CUSTOM_FILTER_VALUE_NO
            ];
        }

        return $fields;
    }

    /**
     * Check if product is new
     *
     * @param int $productId
     * @param int $storeId
     * @return bool
     */
    private function isNew($productId, $storeId)
    {
        return in_array($productId, $this->getNewProductIds($storeId));
    }

    /**
     * Get new product ids
     *
     * @param int $storeId
     * @return array
     */
    private function getNewProductIds($storeId)
    {
        if (empty($this->productIds) || !isset($this->productIds[$storeId])) {
            $this->productIds[$storeId] = $this->provider->getProductIds(false, $storeId);
        }

        return $this->productIds[$storeId];
    }
}
