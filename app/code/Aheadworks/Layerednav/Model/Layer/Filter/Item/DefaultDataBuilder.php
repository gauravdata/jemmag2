<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item;

/**
 * Class DefaultDataBuilder
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item
 */
class DefaultDataBuilder extends AbstractDataBuilder
{
    /**
     * @return array
     */
    public function build()
    {
        $result = $this->_itemsData;
        $this->_itemsData = [];
        return $result;
    }
}
