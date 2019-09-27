<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\Swatches;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Layerednav\Model\Filter\Swatch\Repository as SwatchRepository;
use Aheadworks\Layerednav\Api\Data\FilterExtensionInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Filter\Swatch\Finder as SwatchFinder;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\Swatches
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var SwatchRepository
     */
    private $swatchRepository;

    /**
     * @var SwatchFinder
     */
    private $swatchFinder;

    /**
     * @param SwatchRepository $swatchRepository
     * @param SwatchFinder $swatchFinder
     */
    public function __construct(
        SwatchRepository $swatchRepository,
        SwatchFinder $swatchFinder
    ) {
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
        if ($filterId = (int)$entity->getId()) {
            $swatches = $this->swatchFinder->getByFilterId($filterId);
            /** @var FilterExtensionInterface $extensionAttributes */
            $extensionAttributes = $entity->getExtensionAttributes();
            $extensionAttributes->setSwatches($swatches);
            $entity->setExtensionAttributes($extensionAttributes);
        }
        return $entity;
    }
}
