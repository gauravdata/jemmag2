<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Api\Data\Filter\ModeInterface;
use Aheadworks\Layerednav\Model\Filter\PostDataProcessorInterface;

/**
 * Class Mode
 * @package Aheadworks\Layerednav\Model\Filter\PostDataProcessor
 */
class Mode implements PostDataProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        $storeId = $data['store_id'];

        $attributeData = [];
        if (isset($data[ModeInterface::FILTER_MODES])) {
            foreach ($data[ModeInterface::FILTER_MODES] as $index => $filterMode) {
                if ($filterMode['store_id'] == $storeId) {
                    unset($data[ModeInterface::FILTER_MODES][$index]);
                }
            }
            $attributeData[ModeInterface::FILTER_MODES] = $data[ModeInterface::FILTER_MODES];
        }

        if (!isset($data['default_filter_mode']) || !$data['default_filter_mode']) {
            $attributeData[ModeInterface::FILTER_MODES][] = [
                'store_id' => $data['store_id'],
                'value' => $data['filter_mode']
            ];
        }

        $data['extension_attributes']['filter_mode'] = $attributeData;
        unset($data['default_filter_mode']);
        unset($data['filter_mode']);
        unset($data[ModeInterface::FILTER_MODES]);

        return $data;
    }
}
