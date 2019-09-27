<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Filter\PostDataProcessorInterface;
use Magento\Store\Model\Store;

/**
 * Class Title
 * @package Aheadworks\Layerednav\Model\Filter\PostDataProcessor
 */
class Title implements PostDataProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        $storeId = $data['store_id'];

        if ($storeId == Store::DEFAULT_STORE_ID) {
            $data[FilterInterface::DEFAULT_TITLE] = $data['title'];
        } else {
            if (isset($data[FilterInterface::STOREFRONT_TITLES])) {
                foreach ($data[FilterInterface::STOREFRONT_TITLES] as $index => $title) {
                    if ($title['store_id'] == $storeId) {
                        unset($data[FilterInterface::STOREFRONT_TITLES][$index]);
                    }
                }
            }
            if (!isset($data['default_title_checkbox']) || !$data['default_title_checkbox']) {
                $data[FilterInterface::STOREFRONT_TITLES][] = [
                    'store_id' => $data['store_id'],
                    'value' => $data['title']
                ];
            }
        }

        return $data;
    }
}
