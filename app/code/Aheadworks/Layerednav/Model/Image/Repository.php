<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Image;

use Aheadworks\Layerednav\Api\Data\ImageInterface;
use Aheadworks\Layerednav\Api\Data\ImageInterfaceFactory;
use Aheadworks\Layerednav\Model\Image as ImageModel;
use Aheadworks\Layerednav\Model\ResourceModel\Image\Collection as ImageCollection;
use Aheadworks\Layerednav\Model\ResourceModel\Image\CollectionFactory as ImageCollectionFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Repository
 *
 * @package Aheadworks\Layerednav\Model\Image
 */
class Repository
{
    /**
     * @var ImageInterface[]
     */
    private $instances = [];

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ImageInterfaceFactory
     */
    private $imageFactory;

    /**
     * @var ImageCollectionFactory
     */
    private $imageCollectionFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param EntityManager $entityManager
     * @param ImageInterfaceFactory $imageFactory
     * @param ImageCollectionFactory $imageCollectionFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        EntityManager $entityManager,
        ImageInterfaceFactory $imageFactory,
        ImageCollectionFactory $imageCollectionFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->entityManager = $entityManager;
        $this->imageFactory = $imageFactory;
        $this->imageCollectionFactory = $imageCollectionFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * Retrieve image
     *
     * @param int $imageId
     * @return ImageInterface
     * @throws NoSuchEntityException
     */
    public function get($imageId)
    {
        if (empty($imageId)) {
            throw NoSuchEntityException::singleField('id', $imageId);
        }
        if (!isset($this->instances[$imageId])) {
            /** @var ImageInterface $image */
            $image = $this->imageFactory->create();

            $image = $this->entityManager->load($image, $imageId);
            if (!$image->getId()) {
                throw NoSuchEntityException::singleField('id', $imageId);
            }
            $this->instances[$imageId] = $image;
        }
        return $this->instances[$imageId];
    }

    /**
     * Save image
     *
     * @param ImageInterface $image
     * @return ImageInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function save($image)
    {
        try {
            $this->entityManager->save($image);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        unset($this->instances[$image->getId()]);

        return $this->get($image->getId());
    }

    /**
     * Delete image
     *
     * @param ImageInterface $image
     * @return bool
     * @throws \Exception
     */
    public function delete(ImageInterface $image)
    {
        $this->entityManager->delete($image);
        unset($this->instances[$image->getId()]);

        return true;
    }

    /**
     * Delete image by id
     *
     * @param int $imageId
     * @return bool
     * @throws \Exception
     */
    public function deleteById($imageId)
    {
        /** @var ImageInterface $image */
        $image = $this->get($imageId);
        return $this->delete($image);
    }

    /**
     * Retrieve list of images according to the specified search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ImageInterface[]
     * @throws NoSuchEntityException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var ImageCollection $collection */
        $collection = $this->imageCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, ImageInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        $images = [];
        /** @var ImageModel $item */
        foreach ($collection->getItems() as $item) {
            $image = $this->get($item->getId());
            $images[] = $image;
        }

        return $images;
    }
}
