<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Filter\PostDataProcessorInterface;

/**
 * Class SortOrder
 * @package Aheadworks\Layerednav\Model\Filter\PostDataProcessor
 */
class SortOrder implements PostDataProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        $storeId = $data['store_id'];

        if (isset($data[FilterInterface::SORT_ORDERS])) {
            foreach ($data[FilterInterface::SORT_ORDERS] as $index => $sortOrder) {
                if ($sortOrder['store_id'] == $storeId) {
                    unset($data['sort_orders'][$index]);
                }
            }
        }
        if (!isset($data['default_sort_order']) || !$data['default_sort_order']) {
            $data['sort_orders'][] = [
                'store_id' => $data['store_id'],
                'value' => $data['sort_order']
            ];
        }

        return $data;
    }
}
