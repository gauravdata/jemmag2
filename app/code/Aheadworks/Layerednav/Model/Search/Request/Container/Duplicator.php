<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Request\Container;

use Magento\Framework\Search\Request\BucketInterface;

/**
 * Class Duplicator
 * @package Aheadworks\Layerednav\Model\Search\Request\Container
 */
class Duplicator
{
    /**
     * Duplicate search request container data
     *
     * @param array $data
     * @param string $srcName
     * @param string $destName
     * @param bool $skipDynamicAggregations
     * @return array
     */
    public function perform($data, $srcName, $destName, $skipDynamicAggregations = false)
    {
        if (!isset($data['queries'])
            || !isset($data['query'])
            || !isset($data['aggregations'])
        ) {
            throw new \InvalidArgumentException('Bad container data specified!');
        }

        $destData = $data;
        $query = $destData['queries'][$srcName];
        $query['name'] = $destName;
        unset($destData['queries'][$srcName]);
        $destData['queries'][$destName] = $query;
        $destData['query'] = $destName;

        if ($skipDynamicAggregations) {
            foreach ($destData['aggregations'] as $bucketKey => $bucket) {
                if (isset($bucket['type']) && $bucket['type'] == BucketInterface::TYPE_DYNAMIC) {
                    unset($destData['aggregations'][$bucketKey]);
                }
            }
        }

        return $destData;
    }
}
