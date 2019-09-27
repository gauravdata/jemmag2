<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier;

use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class Context
 * @package Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier
 * @codeCoverageIgnore
 */
class Context extends AbstractSimpleObject
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const STORE_ID             = 'store_id';
    const CUSTOMER_GROUP_ID    = 'customer_group_id';
    /**#@-*/

    /**
     * Get store id
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get customer group id
     *
     * @return int|null
     */
    public function getCustomerGroupId()
    {
        return $this->_get(self::CUSTOMER_GROUP_ID);
    }

    /**
     * Set customer group id
     *
     * @param int $customerGroupId
     * @return $this
     */
    public function setCustomerGroupId($customerGroupId)
    {
        return $this->setData(self::CUSTOMER_GROUP_ID, $customerGroupId);
    }
}
