<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch\Relation\StorefrontTitles;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterfaceFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch as SwatchResourceModel;
use Aheadworks\Layerednav\Model\StorefrontValueResolver;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch\Relation\StorefrontTitles
 */
class ReadHandler implements ExtensionInterface
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
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var StoreValueInterfaceFactory
     */
    private $storeValueFactory;

    /**
     * @var StorefrontValueResolver
     */
    private $storefrontValueResolver;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreValueInterfaceFactory $storeValueFactory
     * @param StorefrontValueResolver $storefrontValueResolver
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        DataObjectHelper $dataObjectHelper,
        StoreValueInterfaceFactory $storeValueFactory,
        StorefrontValueResolver $storefrontValueResolver
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeValueFactory = $storeValueFactory;
        $this->storefrontValueResolver = $storefrontValueResolver;
    }

    /**
     * @param SwatchInterface $entity
     * @param array $arguments
     * @return SwatchInterface
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        if ($swatchId = (int)$entity->getId()) {
            $titles = $this->getTitles($swatchId);
            $currentStorefrontTitle = $this->storefrontValueResolver->getStorefrontValue(
                $titles,
                $arguments['store_id']
            );
            $entity
                ->setStorefrontTitles($titles)
                ->setCurrentStorefrontTitle($currentStorefrontTitle);
        }
        return $entity;
    }

    /**
     * Get storefront titles
     *
     * @param int $swatchId
     * @return StoreValueInterface[]
     * @throws \Exception
     */
    private function getTitles($swatchId)
    {
        $titles = [];
        $titlesData = $this->getTitlesData($swatchId);

        foreach ($titlesData as $titleDataRow) {
            $titleEntity = $this->storeValueFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $titleEntity,
                $titleDataRow,
                StoreValueInterface::class
            );
            $titles[] = $titleEntity;
        }

        return $titles;
    }

    /**
     * Get storefront titles data
     *
     * @param int $swatchId
     * @return array
     * @throws \Exception
     */
    private function getTitlesData($swatchId)
    {
        $connection = $this->getConnection();
        $tableName = $this->getSwatchTitleLinkageTableName();

        $select = $connection
            ->select()
            ->from($tableName)
            ->where('swatch_id = :id');
        $titlesData = $connection->fetchAll(
            $select,
            ['id' => $swatchId]
        );

        return $titlesData;
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
