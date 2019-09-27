<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier;

use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\FilterApplierInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Product\Collection\SalesProvider;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\DB\Select;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class SalesApplier
 * @package Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier
 * @codeCoverageIgnore
 */
class SalesApplier implements FilterApplierInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SalesProvider
     */
    private $provider;

    /**
     * @param StoreManagerInterface $storeManager
     * @param SalesProvider $provider
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        SalesProvider $provider
    ) {
        $this->storeManager = $storeManager;
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Context $context, FilterInterface $filter, Select $select)
    {
        $storeId = $context->getStoreId();
        $customerGroupId = $context->getCustomerGroupId()
            ? $context->getCustomerGroupId()
            : GroupInterface::NOT_LOGGED_IN_ID;

        /** @var ProductCollection $productCollection */
        $productCollection = $this->provider->getProductCollection(true, $customerGroupId, $storeId);
        $productsSelect = $productCollection->getSelect();
        $productsSelect
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns('e.entity_id');

        $select->joinInner(
            ['aw_sales_filter' => $productsSelect],
            'search_index.entity_id = aw_sales_filter.entity_id',
            []
        );

        return true;
    }
}
