<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Request\Container\Cleaner;

/**
 * Class AbstractCleaner
 * @package Aheadworks\Layerednav\Model\Search\Request\Container\Cleaner
 */
abstract class AbstractCleaner implements CleanerInterface
{
    /**
     * Clean not needed search container data
     *
     * @param array $data
     * @param string $filter
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function cleanData($data, $filter)
    {
        if (!isset($data['query'])
            || !isset($data['queries'])
            || !isset($data['filters'])
            || !isset($data['aggregations'])
        ) {
            throw new \InvalidArgumentException('Bad container data specified!');
        }

        $query = $data['query'];
        foreach ($data['queries'][$query]['queryReference'] as $key => $value) {
            if ($value['ref'] == $filter . '_query') {
                unset($data['queries'][$query]['queryReference'][$key]);
            }
        }

        unset($data['queries'][$filter . '_query']);
        unset($data['filters'][$filter . '_filter']);
        unset($data['aggregations'][$filter . '_bucket']);

        return $data;
    }
}
