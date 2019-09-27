<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Plugin\CatalogSearch\Mysql\Aggregation;

use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation\AggregationProviderInterface;
use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation\CustomDataProviderPool;
use Magento\CatalogSearch\Model\Adapter\Mysql\Aggregation\DataProvider as AggregationDataProvider;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\DimensionFactory;
use Magento\Framework\Search\Request\Dimension;
use Magento\Framework\Search\Request\BucketInterface;

/**
 * Class DataProvider
 * @package Aheadworks\Layerednav\Plugin\CatalogSearch\Mysql\Aggregation
 */
class DataProvider
{
    /**
     * @var CustomDataProviderPool
     */
    private $customDataProviderPool;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var DimensionFactory
     */
    private $dimensionFactory;

    /**
     * @param CustomDataProviderPool $customDataProviderPool
     * @param HttpContext $httpContext
     * @param DimensionFactory $dimensionFactory
     */
    public function __construct(
        CustomDataProviderPool $customDataProviderPool,
        HttpContext $httpContext,
        DimensionFactory $dimensionFactory
    ) {
        $this->customDataProviderPool = $customDataProviderPool;
        $this->httpContext = $httpContext;
        $this->dimensionFactory = $dimensionFactory;
    }

    /**
     * Get dataset for custom attributes
     *
     * @param AggregationDataProvider $subject
     * @param \Closure $proceed
     * @param BucketInterface $bucket
     * @param Dimension[] $dimensions
     * @param Table $entityIdsTable
     * @return Select
     */
    public function aroundGetDataSet(
        AggregationDataProvider $subject,
        \Closure $proceed,
        BucketInterface $bucket,
        array $dimensions,
        Table $entityIdsTable
    ) {
        /** @var AggregationProviderInterface|null $customDataProvider */
        $customDataProvider = $this->customDataProviderPool->getAggregationProvider($bucket->getField());
        if ($customDataProvider) {
            $customerGroupId = $this->httpContext->getValue(Context::CONTEXT_GROUP);
            $dimensions[Context::CONTEXT_GROUP] = $this->dimensionFactory->create(
                [
                    'name' => Context::CONTEXT_GROUP,
                    'value' => $customerGroupId
                ]
            );
            return $customDataProvider->getDataSet($dimensions, $entityIdsTable);
        }

        return $proceed($bucket, $dimensions, $entityIdsTable);
    }
}
