<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter;

use Magento\Framework\DataObject;

/**
 * Class Interval
 * @package Aheadworks\Layerednav\Model\Layer\Filter
 */
class Interval extends DataObject
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const FROM  = 'from';
    const TO    = 'to';
    /**#@-*/

    /**
     * Get 'From'
     *
     * @return float
     */
    public function getFrom()
    {
        return $this->getData(self::FROM);
    }

    /**
     * Set 'From'
     *
     * @param float $from
     * @return $this
     */
    public function setFrom($from)
    {
        return $this->setData(self::FROM, $from);
    }

    /**
     * Get 'To'
     *
     * @return float
     */
    public function getTo()
    {
        return $this->getData(self::TO);
    }

    /**
     * Set 'To'
     *
     * @param float $to
     * @return $this
     */
    public function setTo($to)
    {
        return $this->setData(self::TO, $to);
    }

    /**
     * Convert interval to the string representation
     *
     * @return string
     */
    public function __toString()
    {
        return implode('-', [$this->getFrom(), $this->getTo()]);
    }
}
