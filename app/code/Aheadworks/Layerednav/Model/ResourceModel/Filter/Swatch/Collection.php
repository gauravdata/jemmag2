<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch;

use Aheadworks\Layerednav\Model\ResourceModel\AbstractCollection;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Aheadworks\Layerednav\Model\Filter\Swatch;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch as SwatchFilterResourceModel;

/**
 * Class Collection
 *
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = SwatchInterface::ID;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Swatch::class, SwatchFilterResourceModel::class);
    }
}
