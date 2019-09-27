<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui\Component\Modifier;

use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class Mode
 *
 * @package Aheadworks\Layerednav\Ui\Component\Modifier
 */
class Mode implements ModifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $storeId = isset($data['store_id']) ? $data['store_id'] : null;
        $data['default_filter_mode'] = '1';
        /** @var FilterExtensionInterface $extensionAttributes */
        $extensionAttributes = isset($data['extension_attributes']) ? $data['extension_attributes'] : [];
        if (isset($extensionAttributes['filter_mode'])
            && is_array($extensionAttributes['filter_mode'])
        ) {
            $mode = $extensionAttributes['filter_mode'];
            if (isset($mode['filter_modes'])
                && is_array($mode['filter_modes'])
            ) {
                foreach ($mode['filter_modes'] as $filterMode) {
                    if (isset($filterMode[StoreValueInterface::STORE_ID])
                        && $filterMode[StoreValueInterface::STORE_ID] == $storeId
                    ) {
                        $data['default_filter_mode'] = '0';
                        $data['filter_mode'] = isset($filterMode[StoreValueInterface::VALUE])
                            ? $filterMode[StoreValueInterface::VALUE]
                            : '';
                    }
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
