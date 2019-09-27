<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui\Component\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;

/**
 * Class SortOrder
 *
 * @package Aheadworks\Layerednav\Ui\Component\Modifier
 */
class SortOrder implements ModifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $storeId = isset($data['store_id']) ? $data['store_id'] : null;
        if (isset($data[FilterInterface::SORT_ORDERS])
            && is_array($data[FilterInterface::SORT_ORDERS])
        ) {
            foreach ($data[FilterInterface::SORT_ORDERS] as $sortOrder) {
                if (isset($sortOrder[StoreValueInterface::STORE_ID])
                    && $sortOrder[StoreValueInterface::STORE_ID] == $storeId
                ) {
                    $data['default_sort_order'] = '0';
                    $data['sort_order'] = isset($sortOrder[StoreValueInterface::VALUE])
                        ? $sortOrder[StoreValueInterface::VALUE]
                        : '';
                }
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
