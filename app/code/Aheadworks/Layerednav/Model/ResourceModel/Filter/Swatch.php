<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Swatch
 *
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter
 */
class Swatch extends AbstractDb
{
    /**#@+
     * Constants defined for tables
     * used by corresponding entity
     */
    const MAIN_TABLE_NAME            = 'aw_layerednav_filter_swatch';
    const MAIN_TABLE_ID_FIELD_NAME   = 'id';
    const SWATCH_IMAGE_TABLE_NAME    = 'aw_layerednav_filter_swatch_image';
    const SWATCH_TITLE_TABLE_NAME    = 'aw_layerednav_filter_swatch_title';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_ID_FIELD_NAME);
    }
}
