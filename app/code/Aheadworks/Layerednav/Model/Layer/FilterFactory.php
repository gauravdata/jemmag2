<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Layer\Filter as LayerFilter;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\ProviderInterface as ItemProviderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\ProviderFactory as ItemProviderFactory;
use Aheadworks\Layerednav\Model\Layer\Filter\Factory\DataProviderPool as FilterDataProviderPool;
use Aheadworks\Layerednav\Model\Layer\Filter\Factory\DataProviderInterface as FilterDataProviderInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Class FilterFactory
 * @package Aheadworks\Layerednav\Model\Layer
 */
class FilterFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ItemProviderFactory
     */
    private $itemProviderFactory;

    /**
     * @var FilterDataProviderPool
     */
    private $filterDataProviderPool;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ItemProviderFactory $itemProviderFactory
     * @param FilterDataProviderPool $filterDataProviderPool
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ItemProviderFactory $itemProviderFactory,
        FilterDataProviderPool $filterDataProviderPool
    ) {
        $this->objectManager = $objectManager;
        $this->itemProviderFactory = $itemProviderFactory;
        $this->filterDataProviderPool = $filterDataProviderPool;
    }

    /**
     * Create layer filter
     *
     * @param FilterInterface $filterObject
     * @param Layer $layer
     * @param Attribute|null $attribute
     * @return LayerFilter
     * @throws \Exception
     */
    public function create($filterObject, $layer, $attribute = null)
    {
        /** @var ItemProviderInterface $itemsDataProvider */
        $itemsDataProvider = $this->itemProviderFactory->create(
            $filterObject->getType(),
            $filterObject->getStorefrontSortOrder()
        );

        /** @var FilterDataProviderInterface $filterDataProvider */
        $filterDataProvider = $this->filterDataProviderPool->getDataProvider($filterObject->getType());
        $filterData = $filterDataProvider->getData($filterObject, $attribute);
        $filterData[LayerFilter::LAYER] = $layer;

        $data = [
            'itemsProvider' => $itemsDataProvider,
            'data' => $filterData
         ];

        return $this->objectManager->create(LayerFilter::class, $data);
    }
}
