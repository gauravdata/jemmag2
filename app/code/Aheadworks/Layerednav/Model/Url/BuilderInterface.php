<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Url;

/**
 * Interface BuilderInterface
 * @package Aheadworks\Layerednav\Model\Url
 */
interface BuilderInterface
{
    /**
     * Get current url
     *
     * @param string $fromType
     * @return string
     */
    public function getCurrentUrl($fromType);

    /**
     * Get current canonical url
     *
     * @return string
     */
    public function getCurrentCanonicalUrl();
}
