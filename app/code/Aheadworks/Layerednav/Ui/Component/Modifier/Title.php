<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui\Component\Modifier;

use Magento\Store\Model\Store;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;

/**
 * Class Title
 *
 * @package Aheadworks\Layerednav\Ui\Component\Modifier
 */
class Title implements ModifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $data['title'] = isset($data[FilterInterface::DEFAULT_TITLE]) ? $data[FilterInterface::DEFAULT_TITLE] : '';

        $storeId = isset($data['store_id']) ? $data['store_id'] : null;
        if ($storeId == Store::DEFAULT_STORE_ID) {
            $data['default_title_checkbox'] = '0';
        } else {
            $data['default_title_checkbox'] = '1';
            if (isset($data[FilterInterface::STOREFRONT_TITLES])
                && is_array($data[FilterInterface::STOREFRONT_TITLES])
            ) {
                foreach ($data[FilterInterface::STOREFRONT_TITLES] as $storefrontTitle) {
                    if (isset($storefrontTitle[StoreValueInterface::STORE_ID])
                        && $storefrontTitle[StoreValueInterface::STORE_ID] == $storeId
                    ) {
                        $data['default_title_checkbox'] = '0';
                        $data['title'] = isset($storefrontTitle[StoreValueInterface::VALUE])
                            ? $storefrontTitle[StoreValueInterface::VALUE]
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
