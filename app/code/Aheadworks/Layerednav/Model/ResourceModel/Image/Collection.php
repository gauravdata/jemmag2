<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Image;

use Aheadworks\Layerednav\Model\ResourceModel\AbstractCollection;
use Aheadworks\Layerednav\Api\Data\ImageInterface;
use Aheadworks\Layerednav\Model\Image;
use Aheadworks\Layerednav\Model\ResourceModel\Image as ImageResourceModel;

/**
 * Class Collection
 *
 * @package Aheadworks\Layerednav\Model\ResourceModel\Image
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = ImageInterface::ID;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Image::class, ImageResourceModel::class);
    }
}
