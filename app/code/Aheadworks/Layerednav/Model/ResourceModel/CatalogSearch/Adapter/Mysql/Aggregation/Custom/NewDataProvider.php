<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation\Custom;

use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation\AggregationProviderInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Product\Collection\NewProvider;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ScopeInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Select;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Search\Request\Dimension;

/**
 * Class NewDataProvider
 * @package Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation\Custom
 * @codeCoverageIgnore
 */
class NewDataProvider implements AggregationProviderInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var NewProvider
     */
    private $provider;

    /**
     * @param ResourceConnection $resource
     * @param ScopeResolverInterface $scopeResolver
     * @param NewProvider $provider
     */
    public function __construct(
        ResourceConnection $resource,
        ScopeResolverInterface $scopeResolver,
        NewProvider $provider
    ) {
        $this->resource = $resource;
        $this->scopeResolver = $scopeResolver;
        $this->provider = $provider;
    }

    /**
     * Get new data set
     *
     * @param Dimension[] $dimensions
     * @param Table $entityIdsTable
     * @return Select
     * @throws \Zend_Db_Exception
     */
    public function getDataSet(array $dimensions, Table $entityIdsTable)
    {
        /** @var ScopeInterface $scope */
        $scope = $this->scopeResolver->getScope($dimensions['scope']->getValue());
        $scopeId = $scope->getId();

        /** @var ProductCollection $productCollection */
        $productCollection = $this->provider->getProductCollection(true, $scopeId);
        $productsSelect = $productCollection->getSelect();
        $productsSelect
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns('e.entity_id');

        /** @var Select $select */
        $select = $this->getSelect();
        $select
            ->from(
                ['main_table' => $entityIdsTable->getName()],
                []
            )
            ->joinLeft(
                ['product' => $productsSelect],
                'main_table.entity_id = product.entity_id',
                ['value' => new \Zend_Db_Expr('IF(product.entity_id IS NOT NULL, 1, 0)')]
            );

        $select = $this->getSelect()
            ->from(['main_table' => $select]);

        return $select;
    }

    /**
     * Get select
     *
     * @return Select
     */
    private function getSelect()
    {
        return $this->resource->getConnection()->select();
    }
}
