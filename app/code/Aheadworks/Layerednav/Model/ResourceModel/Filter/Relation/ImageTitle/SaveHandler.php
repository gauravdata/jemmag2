<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\ImageTitle;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Filter as FilterResourceModel;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\ImageTitle
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @param FilterInterface $entity
     * @param array $arguments
     * @return FilterInterface
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $filterId = (int)$entity->getId();
        $this->removeOldImageTitlesData($filterId);

        $imageTitlesDataToSave = $this->getImageTitlesDataToSave($entity);
        if (!empty($imageTitlesDataToSave)) {
            $this
                ->getConnection()
                ->insertMultiple(
                    $this->getFilterImageTitleTableName(),
                    $imageTitlesDataToSave
                );
        }

        return $entity;
    }

    /**
     * Remove old filter image titles records
     *
     * @param int $filterId
     * @return bool
     * @throws \Exception
     */
    private function removeOldImageTitlesData($filterId)
    {
        $this
            ->getConnection()
            ->delete(
                $this->getFilterImageTitleTableName(),
                ['filter_id = ?' => $filterId]
            );
        return true;
    }

    /**
     * Retrieve filter image titles data to save
     *
     * @param FilterInterface $filter
     * @return array
     */
    private function getImageTitlesDataToSave($filter)
    {
        $imageTitlesData = [];

        $imageTitles = $filter->getImageTitles();
        if (is_array($imageTitles)) {
            foreach ($imageTitles as $imageTitleRow) {
                $imageTitlesData[] = [
                    'filter_id' => $filter->getId(),
                    'store_id' => $imageTitleRow->getStoreId(),
                    'value' => $imageTitleRow->getValue(),
                ];
            }
        }

        return $imageTitlesData;
    }

    /**
     * Get connection
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(FilterInterface::class)->getEntityConnectionName()
        );
    }

    /**
     * Get filter image title table name
     *
     * @return string
     */
    private function getFilterImageTitleTableName()
    {
        return $this->resourceConnection->getTableName(FilterResourceModel::FILTER_IMAGE_TITLE_TABLE_NAME);
    }
}
