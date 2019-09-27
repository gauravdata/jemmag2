<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\BatchDataMapper;

/**
 * Interface AdditionalFieldsProviderInterface
 * @package Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\BatchDataMapper
 */
interface AdditionalFieldsProviderInterface
{
    /**
     * Get additional fields for data mapper during search indexer based on product ids and store id.
     *
     * @param array $productIds
     * @param int $storeId
     * @return array
     */
    public function getFields(array $productIds, $storeId);
}
