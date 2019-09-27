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
 * Class DisplayState
 *
 * @package Aheadworks\Layerednav\Ui\Component\Modifier
 */
class DisplayState implements ModifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $storeId = isset($data['store_id']) ? $data['store_id'] : null;
        $data['default_display_state'] = '1';
        if (isset($data[FilterInterface::DISPLAY_STATES])
            && is_array($data[FilterInterface::DISPLAY_STATES])
        ) {
            foreach ($data[FilterInterface::DISPLAY_STATES] as $displayState) {
                if (isset($displayState[StoreValueInterface::STORE_ID])
                    && $displayState[StoreValueInterface::STORE_ID] == $storeId
                ) {
                    $data['default_display_state'] = '0';
                    $data['display_state'] = isset($displayState[StoreValueInterface::VALUE])
                        ? $displayState[StoreValueInterface::VALUE]
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
