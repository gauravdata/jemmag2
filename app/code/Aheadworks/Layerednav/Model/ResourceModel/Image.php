<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aheadworks\Layerednav\Model\ResourceModel\Filter as FilterResourceModel;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch as FilterSwatchResourceModel;

/**
 * Class Image
 *
 * @package Aheadworks\Layerednav\Model\ResourceModel
 */
class Image extends AbstractDb
{
    /**#@+
     * Constants defined for tables
     * used by corresponding entity
     */
    const MAIN_TABLE_NAME           = 'aw_layerednav_image';
    const MAIN_TABLE_ID_FIELD_NAME  = 'id';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_ID_FIELD_NAME);
    }

    /**
     * Retrieve array of image ids, related to the specific filter
     *
     * @param int $filterId
     * @return array
     */
    public function getImageIdsByFilter($filterId)
    {
        try {
            $connection = $this->getConnection();
            $linkageTableName = $this->getTable(FilterResourceModel::FILTER_IMAGE_TABLE_NAME);
            $select = $connection->select()
                ->from(
                    $linkageTableName,
                    [
                        'image_id'
                    ]
                )->where('filter_id = :id');
            $imageIds = $connection->fetchCol($select, ['id' => $filterId]);
        } catch (\Exception $exception) {
            $imageIds = [];
        }

        return $imageIds;
    }

    /**
     * Retrieve array of image ids, related to the specific swatch item
     *
     * @param int $swatchId
     * @return array
     */
    public function getImageIdsBySwatch($swatchId)
    {
        try {
            $connection = $this->getConnection();
            $linkageTableName = $this->getTable(FilterSwatchResourceModel::SWATCH_IMAGE_TABLE_NAME);
            $select = $connection->select()
                ->from(
                    $linkageTableName,
                    [
                        'image_id'
                    ]
                )->where('swatch_id = :id');
            $imageIds = $connection->fetchCol($select, ['id' => $swatchId]);
        } catch (\Exception $exception) {
            $imageIds = [];
        }

        return $imageIds;
    }
}
