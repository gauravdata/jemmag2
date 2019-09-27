<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui\Component\Modifier;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;

/**
 * Class Category
 *
 * @package Aheadworks\Layerednav\Ui\Component\Modifier
 */
class Category implements ModifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if ($this->isNeedToPrepareCategoryListStyle($data)) {
            $storeId = isset($data['store_id']) ? $data['store_id'] : null;
            if (isset($data['category_filter_data'])
                && is_array($data['category_filter_data'])
            ) {
                $filterCategory = $data['category_filter_data'];

                if (isset($filterCategory[FilterCategoryInterface::LIST_STYLES])
                    && is_array($filterCategory[FilterCategoryInterface::LIST_STYLES])
                ) {
                    foreach ($filterCategory[FilterCategoryInterface::LIST_STYLES] as $listStyle) {
                        $data['category_list_styles'][] = [
                            'store_id' => isset($listStyle[StoreValueInterface::STORE_ID])
                                ? $listStyle[StoreValueInterface::STORE_ID]
                                : null,
                            'value' => isset($listStyle[StoreValueInterface::VALUE])
                                ? $listStyle[StoreValueInterface::VALUE]
                                : null,
                        ];
                        if (isset($listStyle[StoreValueInterface::STORE_ID])
                            && $listStyle[StoreValueInterface::STORE_ID] == $storeId
                        ) {
                            $data['default_category_list_style'] = '0';
                            $data['category_list_style'] = isset($listStyle[StoreValueInterface::VALUE])
                                ? $listStyle[StoreValueInterface::VALUE]
                                : null;
                        }
                    }
                }
            }
        }

        $data = $this->prepareExcludeCategoryIds($data);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Check if need to prepare category list style
     *
     * @param array $data
     * @return bool
     */
    private function isNeedToPrepareCategoryListStyle($data)
    {
        return isset($data[FilterInterface::TYPE])
            && $data[FilterInterface::TYPE] == FilterInterface::CATEGORY_FILTER;
    }

    /**
     * Prepare excluded category ids field values
     *
     * @param array $data
     * @return array
     */
    private function prepareExcludeCategoryIds($data)
    {
        if (isset($data[FilterInterface::EXCLUDE_CATEGORY_IDS])) {
            $data[FilterInterface::EXCLUDE_CATEGORY_IDS]
                = $this->convertFieldDataToString($data[FilterInterface::EXCLUDE_CATEGORY_IDS]);
        }

        return $data;
    }

    /**
     * Convert field data to string
     *
     * @param mixed $fieldData
     * @return string|string[]
     */
    private function convertFieldDataToString($fieldData)
    {
        $preparedFieldData = $fieldData;
        if (is_array($fieldData)) {
            foreach ($fieldData as $key => $value) {
                if ($value === false) {
                    $preparedFieldData[$key] = '0';
                } else {
                    $preparedFieldData[$key] = (string)$value;
                }
            }
        } else {
            $preparedFieldData = (string)$fieldData;
        }
        return $preparedFieldData;
    }
}
