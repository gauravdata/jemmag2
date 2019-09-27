<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Store;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

/**
 * Class Resolver
 * @package Aheadworks\Layerednav\Model\Store
 */
class Resolver
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Get website id by store id
     *
     * @param int $storeId
     * @return int|null
     */
    public function getWebsiteIdByStoreId($storeId)
    {
        try {
            /** @var StoreInterface $store */
            $store = $this->storeManager->getStore($storeId);
            $websiteId = $store->getWebsiteId();
        } catch (NoSuchEntityException $e) {
            $websiteId = null;
        }

        return $websiteId;
    }

    /**
     * Retrieve array of all stores sorted according to its sort order
     *
     * @return Store[]
     */
    public function getStoresSortedBySortOrder()
    {
        /** @var Store[] $allStores */
        $allStores = $this->storeManager->getStores(true);
        if (is_array($allStores)) {
            usort($allStores, function (Store $storeA, Store $storeB) {
                if ($storeA->getSortOrder() == $storeB->getSortOrder()) {
                    return $storeA->getId() < $storeB->getId() ? -1 : 1;
                }
                return ($storeA->getSortOrder() < $storeB->getSortOrder()) ? -1 : 1;
            });
        }
        return $allStores;
    }
}
