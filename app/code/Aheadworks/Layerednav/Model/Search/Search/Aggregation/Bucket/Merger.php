<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Search\Aggregation\Bucket;

use Magento\Framework\Api\Search\AggregationValueInterface;
use Magento\Framework\Api\Search\BucketInterface;
use Magento\Framework\Search\Response\Aggregation\ValueFactory as AggregationValueFactory;
use Magento\Framework\Search\Response\BucketFactory;

/**
 * Class Merger
 * @package Aheadworks\Layerednav\Model\Search\Search\Aggregation\Bucket
 */
class Merger
{
    /**
     * @var AggregationValueFactory
     */
    private $aggregationValueFactory;

    /**
     * @var BucketFactory
     */
    private $bucketFactory;

    /**
     * @param AggregationValueFactory $aggregationValueFactory
     * @param BucketFactory $bucketFactory
     */
    public function __construct(
        AggregationValueFactory $aggregationValueFactory,
        BucketFactory $bucketFactory
    ) {
        $this->aggregationValueFactory = $aggregationValueFactory;
        $this->bucketFactory = $bucketFactory;
    }

    /**
     * Merge buckets
     *
     * @param BucketInterface $baseBucket
     * @param BucketInterface $extendedBucket
     * @return BucketInterface
     */
    public function merge($baseBucket, $extendedBucket)
    {
        $values = $baseBucket->getValues();

        foreach ($values as $valueIndex => $value) {
            $extendedValue = $this->getValue($extendedBucket, $value->getValue());
            if ($extendedValue) {
                $values[$valueIndex] = clone $extendedValue;
            } else {
                $values[$valueIndex] = $this->aggregationValueFactory->create(
                    [
                        'value' => $value->getValue(),
                        'metrics' => [
                            'value' => $value->getValue(),
                            'count' => 0
                        ],
                    ]
                );
            }
        }

        return $this->bucketFactory->create(
            [
                'name' => $baseBucket->getName(),
                'values' => $values
            ]
        );
    }

    /**
     * Get value
     *
     * @param BucketInterface $bucket
     * @param int $value
     * @return bool|AggregationValueInterface
     */
    private function getValue($bucket, $value)
    {
        $result = false;
        foreach ($bucket->getValues() as $bucketValue) {
            if ($bucketValue->getValue() == $value) {
                $result = $bucketValue;
            }
        }

        return $result;
    }
}
