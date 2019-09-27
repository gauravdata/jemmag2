<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch\Relation\StorefrontTitles;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch as SwatchResourceModel;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch\Relation\StorefrontTitles
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
    public function __construct(MetadataPool $metadataPool, ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @param SwatchInterface $entity
     * @param array $arguments
     * @return SwatchInterface
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $this->removeOldTitles($entity);

        $titlesDataToInsert = $this->getTitlesDataToInsert($entity);
        if (!empty($titlesDataToInsert)) {
            $this->getConnection()->insertMultiple(
                $this->getSwatchTitleLinkageTableName(),
                $titlesDataToInsert
            );
        }

        return $entity;
    }

    /**
     * Remove all old titles for specific swatch
     *
     * @param SwatchInterface $swatch
     * @return bool
     * @throws \Exception
     */
    private function removeOldTitles($swatch)
    {
        $swatchId = $swatch->getId();
        if ($swatchId) {
            $this->getConnection()->delete(
                $this->getSwatchTitleLinkageTableName(),
                [
                    'swatch_id = ?' => $swatchId
                ]
            );
            return true;
        }
        return false;
    }

    /**
     * Retrieve titles data to insert
     *
     * @param SwatchInterface $swatch
     * @return array
     */
    private function getTitlesDataToInsert($swatch)
    {
        $titlesToInsert = [];
        $titles = $swatch->getStorefrontTitles();
        if (is_array($titles)) {
            /** @var StoreValueInterface $titleItem */
            foreach ($titles as $titleItem) {
                $titlesToInsert[] = [
                    'swatch_id'                     => $swatch->getId(),
                    StoreValueInterface::STORE_ID   => $titleItem->getStoreId(),
                    StoreValueInterface::VALUE      => $titleItem->getValue()
                ];
            }
        }

        return $titlesToInsert;
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
            $this->metadataPool->getMetadata(SwatchInterface::class)->getEntityConnectionName()
        );
    }

    /**
     * Get swatch title linkage table name
     *
     * @return string
     */
    private function getSwatchTitleLinkageTableName()
    {
        return $this->resourceConnection->getTableName(SwatchResourceModel::SWATCH_TITLE_TABLE_NAME);
    }
}
