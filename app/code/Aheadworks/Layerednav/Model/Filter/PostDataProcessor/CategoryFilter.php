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
 * Class CategoryFilter
 * @package Aheadworks\Layerednav\Model\Filter\PostDataProcessor
 */
class CategoryFilter implements PostDataProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        $storeId = $data['store_id'];

        if ($data['type'] == FilterInterface::CATEGORY_FILTER) {
            if (isset($data['category_list_styles'])) {
                foreach ($data['category_list_styles'] as $index => $listStyle) {
                    if ($listStyle['store_id'] == $storeId) {
                        unset($data['category_list_styles'][$index]);
                    }
                }
            }
            if (!isset($data['default_category_list_style']) || !$data['default_category_list_style']) {
                $data['category_list_styles'][] = [
                    'store_id' => $data['store_id'],
                    'value' => $data['category_list_style']
                ];
            }
            $data['category_filter_data'][FilterCategoryInterface::LIST_STYLES] = $data['category_list_styles'];
        }

        unset($data['category_list_styles']);
        unset($data['category_list_style']);
        unset($data['default_category_list_style']);

        return $data;
    }
}
