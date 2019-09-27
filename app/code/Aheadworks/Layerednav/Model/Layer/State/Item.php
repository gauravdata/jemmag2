<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\State;

use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class Item
 * @package Aheadworks\Layerednav\Model\Layer\State
 * @codeCoverageIgnore
 */
class Item extends AbstractSimpleObject
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const FILTER_ITEM       = 'filter_item';
    const FILTER_FIELD      = 'filter_field';
    const FILTER_CONDITION  = 'filter_condition';
    const FILTER_OR_OPTION  = 'filter_or_option';
    /**#@-*/

    /**
     * Get filter item
     *
     * @return FilterItemInterface
     */
    public function getFilterItem()
    {
        return $this->_get(self::FILTER_ITEM);
    }

    /**
     * Set filter item
     *
     * @param FilterItemInterface $filterItem
     * @return $this
     */
    public function setFilterItem($filterItem)
    {
        return $this->setData(self::FILTER_ITEM, $filterItem);
    }

    /**
     * Get filter field
     *
     * @return string
     */
    public function getFilterField()
    {
        return $this->_get(self::FILTER_FIELD);
    }

    /**
     * Set filter field
     *
     * @param string $filterField
     * @return $this
     */
    public function setFilterField($filterField)
    {
        return $this->setData(self::FILTER_FIELD, $filterField);
    }

    /**
     * Get filter condition
     *
     * @return array
     */
    public function getFilterCondition()
    {
        return $this->_get(self::FILTER_CONDITION);
    }

    /**
     * Set filter condition
     *
     * @param array $filterCondition
     * @return $this
     */
    public function setFilterCondition($filterCondition)
    {
        return $this->setData(self::FILTER_CONDITION, $filterCondition);
    }

    /**
     * Get filter 'or' option
     *
     * @return bool
     */
    public function getFilterOrOption()
    {
        return $this->_get(self::FILTER_OR_OPTION);
    }

    /**
     * Set filter 'or' option
     *
     * @param bool $filterOrOption
     * @return $this
     */
    public function setFilterOrOption($filterOrOption)
    {
        return $this->setData(self::FILTER_OR_OPTION, $filterOrOption);
    }
}
