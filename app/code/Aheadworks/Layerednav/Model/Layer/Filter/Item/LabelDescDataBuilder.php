<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item;

/**
 * Class LabelDescDataBuilder
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item
 */
class LabelDescDataBuilder extends AbstractDataBuilder
{
    /**
     * @return array
     */
    public function build()
    {
        $result = $this->_itemsData;
        usort($result, [$this, 'labelCompare']);
        $this->_itemsData = [];
        return $result;
    }

    /**
     * Items compare
     *
     * @param array $item1
     * @param array $item2
     * @return int
     */
    private function labelCompare($item1, $item2)
    {
        if ($item1['label'] == $item2['label']) {
            return 0;
        }
        return $item1['label'] > $item2['label'] ? -1 : 1;
    }
}
