<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\ImageTitle;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Filter as FilterResourceModel;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterfaceFactory;
use Aheadworks\Layerednav\Model\Filter\Image\TitleResolver as FilterImageTitleResolver;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\ImageTitle
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
     * @var FilterImageTitleResolver
     */
    private $filterImageTitleResolver;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreValueInterfaceFactory $storeValueFactory
     * @param FilterImageTitleResolver $filterImageTitleResolver
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        DataObjectHelper $dataObjectHelper,
        StoreValueInterfaceFactory $storeValueFactory,
        FilterImageTitleResolver $filterImageTitleResolver
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeValueFactory = $storeValueFactory;
        $this->filterImageTitleResolver = $filterImageTitleResolver;
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
            $imageTitles = $this->getImageTitles($entityId);
            $storeId = isset($arguments['store_id']) ? $arguments['store_id'] : null;
            $entity->setImageTitles($imageTitles);
            $currentImageStorefrontTitle = $this->filterImageTitleResolver->getCurrentImageStorefrontTitle(
                $entity,
                $storeId
            );
            $entity->setImageStorefrontTitle($currentImageStorefrontTitle);
        }
        return $entity;
    }

    /**
     * Retrieve array of image titles for specific filter
     *
     * @param int $filterId
     * @return StoreValueInterface[]
     * @throws \Exception
     */
    private function getImageTitles($filterId)
    {
        $imageTitles = [];
        $imageTitlesData = $this->getImageTitlesData($filterId);

        foreach ($imageTitlesData as $dataRow) {
            /** @var StoreValueInterface $imageTitle */
            $imageTitle = $this->storeValueFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $imageTitle,
                $dataRow,
                StoreValueInterface::class
            );
            $imageTitles[] = $imageTitle;
        }

        return $imageTitles;
    }

    /**
     * Retrieve image titles data array for specific filter
     *
     * @param int $filterId
     * @return array
     * @throws \Exception
     */
    private function getImageTitlesData($filterId)
    {
        $connection = $this->getConnection();
        $select = $connection
            ->select()
            ->from($this->getFilterImageTitleTableName())
            ->where('filter_id = :id');
        $imageTitlesData = $connection
            ->fetchAll(
                $select,
                ['id' => $filterId]
            );

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
