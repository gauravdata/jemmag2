<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\Category;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Aheadworks\Layerednav\Api\Data\FilterCategoryInterfaceFactory;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterfaceFactory;
use Aheadworks\Layerednav\Model\Filter;
use Aheadworks\Layerednav\Model\StorefrontValueResolver;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class ReadHandler
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\Category
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
     * @var FilterCategoryInterfaceFactory
     */
    private $filterCategoryFactory;

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
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param FilterCategoryInterfaceFactory $filterCategoryFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreValueInterfaceFactory $storeValueFactory
     * @param StorefrontValueResolver $storefrontValueResolver
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        FilterCategoryInterfaceFactory $filterCategoryFactory,
        DataObjectHelper $dataObjectHelper,
        StoreValueInterfaceFactory $storeValueFactory,
        StorefrontValueResolver $storefrontValueResolver
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->filterCategoryFactory = $filterCategoryFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeValueFactory = $storeValueFactory;
        $this->storefrontValueResolver = $storefrontValueResolver;
    }

    /**
     * @param FilterInterface|Filter $entity
     * @param array $arguments
     * @return FilterInterface
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        if ($entityId = (int)$entity->getId() && $entity->getType() == FilterInterface::CATEGORY_FILTER) {
            $connection = $this->resourceConnection->getConnectionByName(
                $this->metadataPool->getMetadata(FilterInterface::class)->getEntityConnectionName()
            );
            $select = $connection->select()
                ->from($this->resourceConnection->getTableName('aw_layerednav_filter_category'))
                ->where('filter_id = :id')
                ->where('param_name = :list_style');
            $categoryListStyleData = $connection->fetchAll(
                $select,
                ['id' => $entityId, 'list_style' => FilterCategoryInterface::LIST_PARAM_NAME]
            );

            $listStyles = [];
            foreach ($categoryListStyleData as $listStyle) {
                $categoryListStyleEntity = $this->storeValueFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $categoryListStyleEntity,
                    $listStyle,
                    StoreValueInterface::class
                );
                $listStyles[] = $categoryListStyleEntity;
            }

            /** @var FilterCategoryInterface $filterCategory */
            $filterCategory = $this->filterCategoryFactory->create();
            $filterCategory
                ->setListStyles($listStyles)
                ->setStorefrontListStyle(
                    $this->storefrontValueResolver->getStorefrontValue($listStyles, $arguments['store_id'])
                );

            $entity->setCategoryFilterData($filterCategory);
        }
        return $entity;
    }
}
