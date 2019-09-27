<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper;

/**
 * Interface FieldProviderInterface
 * @package Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper
 */
interface FieldsProviderInterface
{
    /**
     * Get fields
     *
     * @param array $context
     * @return array
     */
    public function getFields($context);
}
