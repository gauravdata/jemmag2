<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui\Context;

use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Checker
 *
 * @package Aheadworks\Layerednav\Ui\Context
 */
class Checker
{
    /**
     * Check if context is not global and switched to website, or store view scope
     *
     * @param ContextInterface $context
     * @return bool
     */
    public function isNotGlobal($context)
    {
        return !empty($context->getRequestParam('store'));
    }
}
