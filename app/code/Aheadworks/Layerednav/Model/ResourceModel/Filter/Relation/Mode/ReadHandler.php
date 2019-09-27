<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\Mode;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\FilterExtensionInterface;
use Aheadworks\Layerednav\Api\Data\FilterExtensionInterfaceFactory;
use Aheadworks\Layerednav\Api\Data\Filter\ModeInterface as FilterModeInterface;
use Aheadworks\Layerednav\Api\Data\Filter\ModeInterfaceFactory as FilterModeInterfaceFactory;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterfaceFactory;
use Aheadworks\Layerednav\Model\StorefrontValueResolver;
use Aheadworks\Layerednav\Model\ResourceModel\Filter as FilterResource;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\DisplayState
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
     * @var FilterModeInterfaceFactory
     */
    private $filterModeFactory;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreValueInterfaceFactory $storeValueFactory
     * @param StorefrontValueResolver $storefrontValueResolver
     * @param FilterModeInterfaceFactory $filterModeFactory
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        DataObjectHelper $dataObjectHelper,
        StoreValueInterfaceFactory $storeValueFactory,
        StorefrontValueResolver $storefrontValueResolver,
        FilterModeInterfaceFactory $filterModeFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeValueFactory = $storeValueFactory;
        $this->storefrontValueResolver = $storefrontValueResolver;
        $this->filterModeFactory = $filterModeFactory;
    }

    /**
     * @param FilterInterface $entity
     * @param array $arguments
     * @return FilterInterface
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        if ($entityId = (int)$entity->getId()) {
            $modes = $this->getModeValues($entityId);

            /** @var FilterModeInterface $filterMode */
            $filterMode = $this->filterModeFactory->create();
            $filterMode
                ->setFilterModes($modes)
                ->setStorefrontFilterMode(
                    $this->storefrontValueResolver->getStorefrontValue($modes, $arguments['store_id'])
                );

            /** @var FilterExtensionInterface $extensionAttributes */
            $extensionAttributes = $entity->getExtensionAttributes();
            $extensionAttributes->setFilterMode($filterMode);
            $entity->setExtensionAttributes($extensionAttributes);
        }
        return $entity;
    }

    /**
     * Get mode values
     *
     * @param int $entityId
     * @return StoreValueInterface[]
     * @throws \Exception
     */
    private function getModeValues($entityId)
    {
        $modes = [];
        $modesData = $this->getModesData($entityId);

        foreach ($modesData as $modeData) {
            $modeEntity = $this->storeValueFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $modeEntity,
                $modeData,
                StoreValueInterface::class
            );
            $modes[] = $modeEntity;
        }

        return $modes;
    }

    /**
     * Get modes data
     *
     * @param int $entityId
     * @return array
     * @throws \Exception
     */
    private function getModesData($entityId)
    {
        $connection = $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(FilterInterface::class)->getEntityConnectionName()
        );
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName(FilterResource::FILTER_MODE_TABLE_NAME))
            ->where('filter_id = :id');
        $modesData = $connection->fetchAll(
            $select,
            ['id' => $entityId]
        );

        return $modesData;
    }
}
