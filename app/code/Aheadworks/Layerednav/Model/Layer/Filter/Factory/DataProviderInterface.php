<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Factory;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Interface DataProviderInterface
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Factory
 */
interface DataProviderInterface
{
    /**
     * Get data
     *
     * @param FilterInterface $filterEntity
     * @param Attribute|null $attribute
     * @return array
     * @throws \Exception
     */
    public function getData(FilterInterface $filterEntity, $attribute = null);
}
