<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Filter
 * @package Aheadworks\Layerednav\Model\ResourceModel
 * @codeCoverageIgnore
 */
class Filter extends AbstractDb
{
    /**#@+
     * Constants defined for tables
     * used by corresponding entity
     */
    const FILTER_TABLE_NAME                 = 'aw_layerednav_filter';
    const FILTER_IMAGE_TABLE_NAME           = 'aw_layerednav_filter_image';
    const FILTER_IMAGE_TITLE_TABLE_NAME     = 'aw_layerednav_filter_image_title';
    const FILTER_MODE_TABLE_NAME            = 'aw_layerednav_filter_mode';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::FILTER_TABLE_NAME, 'id');
    }
}
