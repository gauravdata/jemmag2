<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer;

/**
 * Class Option
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item\\DataProvider\Preparer
 */
class Option
{
    /**
     * Process options
     *
     * @param array $options [['value' => ..., 'label' => ...], ...]
     * @param array $optionCounts [optionId => ['value' => 'optionId', 'count' => ...], ]
     * @param bool $withCountOnly
     * @return array
     */
    public function perform($options, $optionCounts, $withCountOnly = true)
    {
        $result = [];
        foreach ($options as $option) {
            if (is_array($option['value'])) {
                continue;
            }

            $optionValue = $option['value'];
            $optionLabel = $option['label'];

            if ($withCountOnly) {
                if (array_key_exists($optionValue, $optionCounts)
                    && ($optionCounts[$optionValue]['count'] || $optionCounts[$optionValue]['count'] == '0')) {
                    $optionCount = $optionCounts[$optionValue]['count'];
                    $result[] = [
                        'value' => $optionValue,
                        'label' => $optionLabel,
                        'count' => $optionCount
                    ];
                }
            } else {
                $optionCount = isset($optionCounts[$optionValue]) ? $optionCounts[$optionValue]['count'] : 0;
                $result[] = [
                    'value' => $optionValue,
                    'label' => $optionLabel,
                    'count' => $optionCount
                ];
            }
        }

        return $result;
    }
}
