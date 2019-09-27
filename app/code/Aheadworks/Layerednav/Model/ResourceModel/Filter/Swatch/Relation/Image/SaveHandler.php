<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch\Relation\Image;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch as FilterSwatchResourceModel;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Aheadworks\Layerednav\Model\Image\Repository as ImageRepository;
use Aheadworks\Layerednav\Model\ResourceModel\Image as ImageResourceModel;
use Aheadworks\Layerednav\Api\Data\ImageInterface;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch\Relation\Image
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
        /** @var SwatchInterface $entity */
        $this->deleteOldImages($entity->getId());
        $imageToSave = $this->getImageToSave($entity);
        if ($imageToSave) {
            $savedImage = $this->imageRepository->save($imageToSave);
            $this->addLinkBetweenSwatchAndImage($entity->getId(), $savedImage->getId());
        }
        return $entity;
    }

    /**
     * Remove old images, related to the swatch item
     *
     * @param int $swatchId
     * @return bool
     * @throws \Exception
     */
    private function deleteOldImages($swatchId)
    {
        $imageIds = $this->imageResourceModel->getImageIdsBySwatch($swatchId);
        foreach ($imageIds as $id) {
            $this->imageRepository->deleteById($id);
        }
        return true;
    }

    /**
     * Retrieve prepared to save swatch image
     *
     * @param SwatchInterface $swatch
     * @return ImageInterface
     */
    private function getImageToSave($swatch)
    {
        $image = $swatch->getImage();
        if ($image) {
            if (empty($image->getName())) {
                $image->setName($image->getFileName());
            }
        }
        return $image;
    }

    /**
     * Add link between swatch item and saved image to the corresponding table
     *
     * @param int $swatchId
     * @param int $imageId
     * @return bool
     * @throws \Exception
     */
    private function addLinkBetweenSwatchAndImage($swatchId, $imageId)
    {
        $connection = $this->getConnection();
        $swatchImageLinkageTableName = $this->getSwatchImageLinkageTableName();
        $linkageData = [
            'swatch_id' => $swatchId,
            'image_id' => $imageId,
        ];
        $connection->insert(
            $swatchImageLinkageTableName,
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
     * Get swatch image linkage table name
     *
     * @return string
     */
    private function getSwatchImageLinkageTableName()
    {
        return $this->resourceConnection->getTableName(FilterSwatchResourceModel::SWATCH_IMAGE_TABLE_NAME);
    }
}
