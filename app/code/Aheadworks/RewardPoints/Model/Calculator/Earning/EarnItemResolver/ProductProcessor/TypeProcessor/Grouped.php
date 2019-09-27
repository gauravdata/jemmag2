<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor\TypeProcessor;

use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor\TypeProcessorInterface;

/**
 * Class Grouped
 * @package Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor\TypeProcessor
 */
class Grouped implements TypeProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEarnItems($product, $beforeTax = true)
    {
        return [];
    }
}
