<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Filter\PostDataProcessorInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;

/**
 * Class ImageTitle
 *
 * @package Aheadworks\Layerednav\Model\Filter\PostDataProcessor
 */
class ImageTitle implements PostDataProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        $storeId = isset($data['store_id']) ? $data['store_id'] : null;

        if (isset($data[FilterInterface::IMAGE_TITLES])) {
            foreach ($data[FilterInterface::IMAGE_TITLES] as $index => $imageTitle) {
                if ($imageTitle[StoreValueInterface::STORE_ID] == $storeId) {
                    unset($data[FilterInterface::IMAGE_TITLES][$index]);
                }
            }
        }
        if ((!isset($data['default_image_title']) || !$data['default_image_title']) && isset($data['image_title'])) {
            $data[FilterInterface::IMAGE_TITLES][] = [
                StoreValueInterface::STORE_ID   => $storeId,
                StoreValueInterface::VALUE      => $data['image_title']
            ];
        }

        return $data;
    }
}
