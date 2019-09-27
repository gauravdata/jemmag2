<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter\Swatch;

use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterfaceFactory;
use Aheadworks\Layerednav\Model\Filter\Swatch as SwatchModel;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch\Collection as SwatchCollection;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch\CollectionFactory as SwatchCollectionFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Repository
 *
 * @package Aheadworks\Layerednav\Model\Filter\Swatch
 */
class Repository
{
    /**
     * @var SwatchInterface[]
     */
    private $instances = [];

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var SwatchInterfaceFactory
     */
    private $swatchFactory;

    /**
     * @var SwatchCollectionFactory
     */
    private $swatchCollectionFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param EntityManager $entityManager
     * @param SwatchInterfaceFactory $swatchFactory
     * @param SwatchCollectionFactory $swatchCollectionFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        EntityManager $entityManager,
        SwatchInterfaceFactory $swatchFactory,
        SwatchCollectionFactory $swatchCollectionFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->entityManager = $entityManager;
        $this->swatchFactory = $swatchFactory;
        $this->swatchCollectionFactory = $swatchCollectionFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve swatch with data for specific store
     *
     * @param int $swatchId
     * @param int|null $storeId
     * @return SwatchInterface
     * @throws NoSuchEntityException
     */
    public function get($swatchId, $storeId = null)
    {
        if (!isset($this->instances[$swatchId])) {
            /** @var SwatchInterface $image */
            $swatch = $this->swatchFactory->create();

            $storeId = $storeId ? : $this->storeManager->getStore()->getId();
            $arguments = ['store_id' => $storeId];

            $swatch = $this->entityManager->load($swatch, $swatchId, $arguments);
            if (!$swatch->getId()) {
                throw NoSuchEntityException::singleField('id', $swatchId);
            }
            $this->instances[$swatchId] = $swatch;
        }
        return $this->instances[$swatchId];
    }

    /**
     * Save swatch
     *
     * @param SwatchInterface $swatch
     * @param int|null $storeId
     * @return SwatchInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function save($swatch, $storeId = null)
    {
        try {
            $this->entityManager->save($swatch);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        unset($this->instances[$swatch->getId()]);

        return $this->get($swatch->getId(), $storeId);
    }

    /**
     * Delete swatch
     *
     * @param SwatchInterface $swatch
     * @return bool
     * @throws \Exception
     */
    public function delete($swatch)
    {
        $this->entityManager->delete($swatch);
        unset($this->instances[$swatch->getId()]);

        return true;
    }

    /**
     * Delete swatch by id
     *
     * @param int $swatchId
     * @return bool
     * @throws \Exception
     */
    public function deleteById($swatchId)
    {
        /** @var SwatchInterface $swatch */
        $swatch = $this->get($swatchId);
        return $this->delete($swatch);
    }

    /**
     * Retrieve list of swatches according to the specified search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param int|null $storeId
     * @return SwatchInterface[]
     * @throws NoSuchEntityException
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var SwatchCollection $collection */
        $collection = $this->swatchCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, SwatchInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        $storeId = $storeId ? : $this->storeManager->getStore()->getId();

        $swatches = [];
        /** @var SwatchModel $item */
        foreach ($collection->getItems() as $item) {
            $swatch = $this->get($item->getId(), $storeId);
            $swatches[] = $swatch;
        }

        return $swatches;
    }
}
