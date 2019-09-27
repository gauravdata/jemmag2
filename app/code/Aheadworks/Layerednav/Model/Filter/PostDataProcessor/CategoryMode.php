<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Filter\PostDataProcessorInterface;

/**
 * Class CategoryMode
 * @package Aheadworks\Layerednav\Model\Filter\PostDataProcessor
 */
class CategoryMode implements PostDataProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if (isset($data['category_mode'])
            && $data['category_mode'] == FilterInterface::CATEGORY_MODE_EXCLUDE
            && !isset($data['exclude_category_ids'])) {
            $data['exclude_category_ids'] = [];
        }

        return $data;
    }
}
