<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\Swatches;

use Aheadworks\Layerednav\Api\Data\FilterExtensionInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Layerednav\Model\Filter\Swatch\Repository as SwatchRepository;
use Aheadworks\Layerednav\Model\Filter\Swatch\Finder as SwatchFinder;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\Swatches
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
     * @var SwatchRepository
     */
    private $swatchRepository;

    /**
     * @var SwatchFinder
     */
    private $swatchFinder;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param SwatchRepository $swatchRepository
     * @param SwatchFinder $swatchFinder
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        SwatchRepository $swatchRepository,
        SwatchFinder $swatchFinder
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->swatchRepository = $swatchRepository;
        $this->swatchFinder = $swatchFinder;
    }

    /**
     * @param FilterInterface $entity
     * @param array $arguments
     * @return FilterInterface
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $this->removeOldSwatches($entity);

        $swatchesToSave = $this->getSwatchesToSave($entity);
        if (!empty($swatchesToSave)) {
            foreach ($swatchesToSave as $swatch) {
                $this->swatchRepository->save($swatch);
            }
        }

        return $entity;
    }

    /**
     * Remove all old swatches for specific filter
     *
     * @param FilterInterface $filter
     * @return bool
     * @throws \Exception
     */
    private function removeOldSwatches($filter)
    {
        $filterId = (int)$filter->getId();
        if ($filterId) {
            $swatches = $this->swatchFinder->getByFilterId($filterId);
            foreach ($swatches as $swatch) {
                $this->swatchRepository->delete($swatch);
            }
            return true;
        }
        return false;
    }

    /**
     * Retrieve array of filter swatches to save
     *
     * @param FilterInterface $filter
     * @return SwatchInterface[]
     */
    private function getSwatchesToSave($filter)
    {
        $swatches = [];
        /** @var FilterExtensionInterface $extensionAttributes */
        $extensionAttributes = $filter->getExtensionAttributes();
        if ($extensionAttributes->getSwatches()) {
            /** @var SwatchInterface[] $swatches */
            $swatches = $extensionAttributes->getSwatches();
            foreach ($swatches as $swatchItem) {
                $swatchItem->setFilterId($filter->getId());
                if (empty($swatchItem->getOptionId())) {
                    $swatchItem->setOptionId(null);
                }
            }
        }
        return $swatches;
    }
}
