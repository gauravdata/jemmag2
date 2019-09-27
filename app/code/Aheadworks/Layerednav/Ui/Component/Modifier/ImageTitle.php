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
 * Class ImageTitle
 *
 * @package Aheadworks\Layerednav\Ui\Component\Modifier
 */
class ImageTitle implements ModifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $storeId = isset($data['store_id']) ? $data['store_id'] : null;
        $data['default_image_title'] = '1';
        if (isset($data[FilterInterface::IMAGE_TITLES])
            && is_array($data[FilterInterface::IMAGE_TITLES])
        ) {
            foreach ($data[FilterInterface::IMAGE_TITLES] as $imageTitle) {
                if (isset($imageTitle[StoreValueInterface::STORE_ID])
                    && $imageTitle[StoreValueInterface::STORE_ID] == $storeId
                ) {
                    $data['default_image_title'] = '0';
                    $data['image_title'] = isset($imageTitle[StoreValueInterface::VALUE])
                        ? $imageTitle[StoreValueInterface::VALUE]
                        : '';
                }
            }
        }

        if (empty($data['image_title'])
            && isset($data['title'])
        ) {
            $data['image_title'] = $data['title'];
        }

        return $data;
    }
}
