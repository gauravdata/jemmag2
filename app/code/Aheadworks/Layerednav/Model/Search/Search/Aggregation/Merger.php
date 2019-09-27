<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Search\Aggregation;

use Aheadworks\Layerednav\Model\Search\Search\Aggregation\Bucket\Merger as BucketMerger;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\AggregationInterfaceFactory;
use Magento\Framework\Api\Search\BucketInterface;

/**
 * Class Merger
 * @package Aheadworks\Layerednav\Model\Search\Search\Aggregation
 */
class Merger
{
    /**
     * @var AggregationInterfaceFactory
     */
    private $aggregationFactory;

    /**
     * @var BucketMerger
     */
    private $bucketMerger;

    /**
     * @param AggregationInterfaceFactory $aggregationFactory
     * @param BucketMerger $bucketMerger
     */
    public function __construct(
        AggregationInterfaceFactory $aggregationFactory,
        BucketMerger $bucketMerger
    ) {
        $this->aggregationFactory = $aggregationFactory;
        $this->bucketMerger = $bucketMerger;
    }

    /**
     * Merge aggregations
     *
     * @param AggregationInterface $baseAggregation
     * @param AggregationInterface[] $aggregations
     * @param bool|false $mergeBucketValues
     * @return AggregationInterface
     */
    public function merge($baseAggregation, $aggregations, $mergeBucketValues = false)
    {
        /** @var BucketInterface[] $buckets */
        $buckets = $baseAggregation->getBuckets();

        /** @var AggregationInterface $aggregation */
        foreach ($aggregations as $aggregation) {
            $bucketsToMerge = $aggregation->getBuckets();
            foreach ($bucketsToMerge as $bucketToMerge) {
                if ($mergeBucketValues && isset($buckets[$bucketToMerge->getName()])) {
                    $buckets[$bucketToMerge->getName()] = $this->bucketMerger->merge(
                        $buckets[$bucketToMerge->getName()],
                        $bucketToMerge
                    );
                } else {
                    $buckets[$bucketToMerge->getName()] = $bucketToMerge;
                }
            }
        }

        /** @var AggregationInterface $aggregations */
        $aggregation = $this->aggregationFactory->create(['buckets' => $buckets]);

        return $aggregation;
    }
}
