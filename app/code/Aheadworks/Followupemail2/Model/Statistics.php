<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Followupemail2\Model;

use Aheadworks\Followupemail2\Model\ResourceModel\Statistics as StatisticsResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Statistics
 * @package Aheadworks\Followupemail2\Model
 * @codeCoverageIgnore
 */
class Statistics extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(StatisticsResource::class);
    }
}
