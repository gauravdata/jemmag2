<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Plugin\Elasticsearch;

use Aheadworks\Layerednav\Model\Config;
use Magento\Elasticsearch\SearchAdapter\Aggregation\Builder\Dynamic;
use Magento\Framework\Search\Dynamic\DataProviderInterface;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;

/**
 * Class DynamicAggregationBuilder
 * @package Aheadworks\Layerednav\Plugin\Elasticsearch
 */
class DynamicAggregationBuilder
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Modify dynamic aggregation builder result
     *
     * @param Dynamic $subject
     * @param array $result
     * @param RequestBucketInterface $bucket
     * @param array $dimensions
     * @param array $queryResult
     * @param DataProviderInterface $dataProvider
     * @return array
     */
    public function afterBuild(
        Dynamic $subject,
        $result,
        RequestBucketInterface $bucket,
        array $dimensions,
        array $queryResult,
        DataProviderInterface $dataProvider
    ) {
        if ($this->config->isManualFromToPriceFilterEnabled()
            && $bucket->getName() == 'price_bucket'
        ) {
            if (isset($queryResult['aggregations'][$bucket->getName()])) {
                $bucketStats = $queryResult['aggregations'][$bucket->getName()];
                $result['stats'] = [
                    'value' => 'stats',
                    'min' => isset($bucketStats['min']) ? $bucketStats['min'] : 0,
                    'max' => isset($bucketStats['max']) ? $bucketStats['max'] : 0,
                    'count' => isset($bucketStats['count']) ? $bucketStats['count'] : 0
                ];
            } else {
                $result['stats'] = [
                    'value' => 'stats',
                    'min' => 0,
                    'max' => 0,
                    'count' => 0
                ];
            }
        }

        return $result;
    }
}
