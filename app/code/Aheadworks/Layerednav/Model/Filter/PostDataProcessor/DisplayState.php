<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Filter\PostDataProcessorInterface;

/**
 * Class DisplayState
 * @package Aheadworks\Layerednav\Model\Filter\PostDataProcessor
 */
class DisplayState implements PostDataProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        $storeId = $data['store_id'];

        if (isset($data[FilterInterface::DISPLAY_STATES])) {
            foreach ($data[FilterInterface::DISPLAY_STATES] as $index => $displayState) {
                if ($displayState['store_id'] == $storeId) {
                    unset($data[FilterInterface::DISPLAY_STATES][$index]);
                }
            }
        }
        if (!isset($data['default_display_state']) || !$data['default_display_state']) {
            $data[FilterInterface::DISPLAY_STATES][] = [
                'store_id' => $data['store_id'],
                'value' => $data['display_state']
            ];
        }

        return $data;
    }
}
