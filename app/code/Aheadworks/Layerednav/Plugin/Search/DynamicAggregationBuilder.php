<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Plugin\Search;

use Aheadworks\Layerednav\Model\Config;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Dynamic;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;
use Magento\Framework\Search\Dynamic\EntityStorageFactory;
use Magento\Framework\Search\Dynamic\DataProviderInterface as DynamicDataProviderInterface;
use Magento\Framework\Search\Dynamic\EntityStorage;

/**
 * Class DynamicAggregationBuilder
 * @package Aheadworks\Layerednav\Plugin\Search
 */
class DynamicAggregationBuilder
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var DynamicDataProviderInterface
     */
    private $dataProvider;

    /**
     * @var EntityStorageFactory
     */
    private $entityStorageFactory;

    /**
     * @param Config $config
     * @param DynamicDataProviderInterface $dataProvider
     * @param EntityStorageFactory $entityStorageFactory
     */
    public function __construct(
        Config $config,
        DynamicDataProviderInterface $dataProvider,
        EntityStorageFactory $entityStorageFactory
    ) {
        $this->config = $config;
        $this->dataProvider = $dataProvider;
        $this->entityStorageFactory = $entityStorageFactory;
    }

    /**
     * Modify dynamic aggregation builder result
     *
     * @param Dynamic $subject
     * @param array $result
     * @param DataProviderInterface $dataProvider
     * @param array $dimensions
     * @param RequestBucketInterface $bucket
     * @param Table $entityIdsTable
     * @return array
     */
    public function afterBuild(
        Dynamic $subject,
        $result,
        DataProviderInterface $dataProvider,
        array $dimensions,
        RequestBucketInterface $bucket,
        Table $entityIdsTable
    ) {
        if ($this->config->isManualFromToPriceFilterEnabled()
            && $bucket->getName() == 'price_bucket'
        ) {
            /** @var EntityStorage $entityStorage */
            $entityStorage = $this->entityStorageFactory->create($entityIdsTable);
            $aggregations = $this->dataProvider->getAggregations($entityStorage);

            $result['stats'] = [
                'value' => 'stats',
                'min' => isset($aggregations['min']) ? $aggregations['min'] : 0,
                'max' => isset($aggregations['max']) ? $aggregations['max'] : 0,
                'count' => isset($aggregations['count']) ? $aggregations['count'] : 0
            ];
        }

        return $result;
    }
}
