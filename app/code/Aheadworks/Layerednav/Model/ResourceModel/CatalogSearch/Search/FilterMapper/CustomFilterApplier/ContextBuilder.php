<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier;

use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ContextBuilder
 * @package Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier
 */
class ContextBuilder
{
    /**
     * @var ContextFactory
     */
    private $contextFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @param ContextFactory $contextFactory
     * @param StoreManagerInterface $storeManager
     * @param HttpContext $httpContext
     */
    public function __construct(
        ContextFactory $contextFactory,
        StoreManagerInterface $storeManager,
        HttpContext $httpContext
    ) {
        $this->contextFactory = $contextFactory;
        $this->storeManager = $storeManager;
        $this->httpContext = $httpContext;
    }

    /**
     * Build context
     *
     * @param int|null $storeId
     * @param int|null $customerGroupId
     * @return Context
     */
    public function build($storeId = null, $customerGroupId = null)
    {
        /** @var Context $context */
        $context = $this->contextFactory->create();

        if ($storeId === null) {
            try {
                $storeId = $this->storeManager->getStore()->getId();
            } catch (NoSuchEntityException $e) {
                $storeId = Store::DEFAULT_STORE_ID;
            }
        }

        if ($customerGroupId === null) {
            $customerGroupId = $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP);
        }

        $context
            ->setStoreId($storeId)
            ->setCustomerGroupId($customerGroupId);

        return $context;
    }
}
