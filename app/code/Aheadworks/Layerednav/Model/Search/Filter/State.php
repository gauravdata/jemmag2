<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Filter;

/**
 * Class State
 * @package Aheadworks\Layerednav\Model\Search\Filter
 */
class State
{
    /**
     * 'Do not use base category' flag
     */
    const DO_NOT_USE_BASE_CATEGORY = 'do_not_use_base_category';

    /**
     * @var bool[]
     */
    private $flags = [];

    /**
     * State constructor.
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Check if the flag is set
     *
     * @param string $code
     * @return bool
     */
    public function isSetFlag($code)
    {
        if (isset($this->flags[$code])) {
            return true;
        }

        return false;
    }

    /**
     * Set flag
     *
     * @param string $code
     * @return $this
     */
    public function setFlag($code)
    {
        $this->flags[$code] = true;

        return $this;
    }

    /**
     * Check flag "Do not use base category"
     *
     * @return $this
     */
    public function isDoNotUseBaseCategoryFlagSet()
    {
        return $this->isSetFlag(self::DO_NOT_USE_BASE_CATEGORY);
    }

    /**
     * Set flag "Do not use base category"
     *
     * @return $this
     */
    public function setDoNotUseBaseCategoryFlag()
    {
        return $this->setFlag(self::DO_NOT_USE_BASE_CATEGORY);
    }

    /**
     * Reset state
     *
     * @return $this
     */
    public function reset()
    {
        $this->flags = [];

        return $this;
    }
}
