<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model;

use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Magento\Store\Model\Store;

/**
 * Class StorefrontValueResolver
 * @package Aheadworks\Layerednav\Model
 */
class StorefrontValueResolver
{
    /**
     * Retrieve storefront value
     *
     * @param StoreValueInterface[] $objects
     * @param int $storeId
     * @param string|int|null $defaultValue
     * @return string|null
     */
    public function getStorefrontValue($objects, $storeId, $defaultValue = null)
    {
        $storefrontValue = null;

        foreach ($objects as $object) {
            if ($object->getStoreId() == $storeId) {
                $storefrontValue = $object->getValue();
                break;
            } elseif ($object->getStoreId() == Store::DEFAULT_STORE_ID) {
                $defaultValue = $object->getValue();
            }
        }

        if (empty($storefrontValue) && !empty($defaultValue)) {
            $storefrontValue = $defaultValue;
        }

        return $storefrontValue;
    }
}
