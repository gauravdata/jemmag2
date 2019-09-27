<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Followupemail2\Model\Order;

use Aheadworks\Followupemail2\Model\ResourceModel\Order\Quote as OrderQuoteResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Quote
 * @package Aheadworks\Followupemail2\Model\Order
 * @codeCoverageIgnore
 */
class Quote extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(OrderQuoteResource::class);
    }
}
