<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Search\Aggregation\Bucket;

/**
 * Class Resolver
 * @package Aheadworks\Layerednav\Model\Search\Search\Aggregation\Bucket
 */
class NameResolver
{
    /**
     * Get bucket name
     *
     * @param string $field
     * @return string
     */
    public function getName($field)
    {
        if ($field == 'category_ids_query') {
            $bucketName = 'category_bucket';
        } else {
            $bucketName = $field . '_bucket';
        }

        return $bucketName;
    }
}
