<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier;

use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\FilterApplierInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Product\Collection\NewProvider;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\DB\Select;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class NewApplier
 * @package Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier
 * @codeCoverageIgnore
 */
class NewApplier implements FilterApplierInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var NewProvider
     */
    private $provider;

    /**
     * @param StoreManagerInterface $storeManager
     * @param NewProvider $provider
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        NewProvider $provider
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

        /** @var ProductCollection $productCollection */
        $productCollection = $this->provider->getProductCollection(true, $storeId);
        $productsSelect = $productCollection->getSelect();
        $productsSelect
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns('e.entity_id');

        $select->joinInner(
            ['aw_new_filter' => $productsSelect],
            'search_index.entity_id = aw_new_filter.entity_id',
            []
        );

        return true;
    }
}
