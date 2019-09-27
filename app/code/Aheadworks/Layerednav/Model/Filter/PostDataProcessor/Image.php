<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Filter\PostDataProcessorInterface;

/**
 * Class Image
 * @package Aheadworks\Layerednav\Model\Filter\PostDataProcessor
 */
class Image implements PostDataProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        $imageData = null;
        if (isset($data[FilterInterface::IMAGE])
            && is_array($data[FilterInterface::IMAGE])
            && count($data[FilterInterface::IMAGE]) > 0
        ) {
            $firstImage = reset($data[FilterInterface::IMAGE]);
            if (is_array($firstImage)) {
                $imageData = $firstImage;
            }
        }
        $data[FilterInterface::IMAGE] = $imageData;

        return $data;
    }
}
