<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch\Relation\Image;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Aheadworks\Layerednav\Model\Image\Repository as ImageRepository;
use Aheadworks\Layerednav\Model\ResourceModel\Image as ImageResourceModel;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch\Relation\Image
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @var ImageResourceModel
     */
    private $imageResourceModel;

    /**
     * @param ImageRepository $imageRepository
     * @param ImageResourceModel $imageResourceModel
     */
    public function __construct(
        ImageRepository $imageRepository,
        ImageResourceModel $imageResourceModel
    ) {
        $this->imageRepository = $imageRepository;
        $this->imageResourceModel = $imageResourceModel;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, $arguments = [])
    {
        /** @var SwatchInterface $entity */
        if ($swatchId = (int)$entity->getId()) {
            $imageIds = $this->imageResourceModel->getImageIdsBySwatch($swatchId);
            if (count($imageIds)) {
                $firstImageId = reset($imageIds);
                $image = $this->imageRepository->get($firstImageId);
                $entity->setImage($image);
            }
        }
        return $entity;
    }
}
