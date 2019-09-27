<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\Image;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Filter as FilterResourceModel;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Aheadworks\Layerednav\Model\Image\Repository as ImageRepository;
use Aheadworks\Layerednav\Model\ResourceModel\Image as ImageResourceModel;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\Image
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
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @var ImageResourceModel
     */
    private $imageResourceModel;

    /**
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     * @param ImageRepository $imageRepository
     * @param ImageResourceModel $imageResourceModel
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool,
        ImageRepository $imageRepository,
        ImageResourceModel $imageResourceModel
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->imageRepository = $imageRepository;
        $this->imageResourceModel = $imageResourceModel;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, $arguments = [])
    {
        /** @var FilterInterface $entity */
        $this->deleteOldImages($entity->getId());
        $imageToSave = $entity->getImage();
        if ($imageToSave) {
            $savedImage = $this->imageRepository->save($imageToSave);
            $this->addLinkBetweenFilterAndImage($entity->getId(), $savedImage->getId());
        }
        return $entity;
    }

    /**
     * Remove old images, related to the filter
     *
     * @param int $filterId
     * @return bool
     * @throws \Exception
     */
    private function deleteOldImages($filterId)
    {
        $imageIds = $this->imageResourceModel->getImageIdsByFilter($filterId);
        foreach ($imageIds as $id) {
            $this->imageRepository->deleteById($id);
        }
        return true;
    }

    /**
     * Add link between filter and saved image to the corresponding table
     *
     * @param int $filterId
     * @param int $imageId
     * @return bool
     * @throws \Exception
     */
    private function addLinkBetweenFilterAndImage($filterId, $imageId)
    {
        $connection = $this->getConnection();
        $filterImageLinkageTableName = $this->getFilterImageLinkageTableName();
        $linkageData = [
            'filter_id' => $filterId,
            'image_id' => $imageId,
        ];
        $connection->insert(
            $filterImageLinkageTableName,
            $linkageData
        );
        return true;
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
     * Get filter image linkage table name
     *
     * @return string
     */
    private function getFilterImageLinkageTableName()
    {
        return $this->resourceConnection->getTableName(FilterResourceModel::FILTER_IMAGE_TABLE_NAME);
    }
}
