<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Product\Collection;

use Aheadworks\Layerednav\Model\DateResolver;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as ResourceAttribute;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class AbstractProvider
 * @package Aheadworks\Layerednav\Model\ResourceModel\Product\Collection
 * @codeCoverageIgnore
 */
abstract class AbstractProvider
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var ResourceAttribute
     */
    protected $resourceAttribute;

    /**
     * @var DateResolver
     */
    protected $dateResolver;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var ProductVisibility
     */
    protected $productVisibility;

    /**
     * @var int[]
     */
    protected $dateAttrIds;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceAttribute $resourceAttribute
     * @param DateResolver $dateResolver
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ProductVisibility $productVisibility
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceAttribute $resourceAttribute,
        DateResolver $dateResolver,
        ProductCollectionFactory $productCollectionFactory,
        ProductVisibility $productVisibility
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceAttribute = $resourceAttribute;
        $this->dateResolver = $dateResolver;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productVisibility = $productVisibility;
    }

    /**
     * Get attribute code for 'from' date
     *
     * @return string
     */
    abstract protected function getDateFromAttrCode();

    /**
     * Get attribute code for 'to' date
     *
     * @return string
     */
    abstract protected function getDateToAttrCode();

    /**
     * Get base product collection
     *
     * @param bool|false $withVisibility
     * @param int|null $storeId
     * @return Collection
     * @throws \Exception
     */
    public function getBaseProductCollection($withVisibility = false, $storeId = null)
    {
        /** @var Collection $collection */
        $collection = $this->productCollectionFactory->create();

        if ($storeId) {
            $collection->addStoreFilter($storeId);
        }

        if ($withVisibility) {
            /** @var string[] $visibility */
            $visibility = $this->productVisibility->getVisibleInCatalogIds();
            $collection->setVisibility($visibility);
        }

        return $collection;
    }

    /**
     * @param Collection $collection
     * @param string $attributeCode
     * @param string $value
     * @param string $condition
     * @param int $storeId
     * @throws \Exception
     */
    protected function addDateFilter($collection, $attributeCode, $value, $condition, $storeId)
    {
        $connection = $collection->getConnection();

        $tableAlias = $collection->getTable('catalog_product_entity_datetime');
        $linkFieldName = $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();

        $dateDefaultAlias = $attributeCode . '_default';
        $dateChildrenAlias = $attributeCode . '_children';

        $dateAttrId = $this->getDateAttrId($attributeCode);
        $childLinkAlias = $this->joinChildProducts($collection);

        $collection
            ->getSelect()
            ->joinLeft(
                [$dateDefaultAlias => $tableAlias],
                "({$dateDefaultAlias}.{$linkFieldName} = e.{$linkFieldName})"
                . " AND ({$dateDefaultAlias}.attribute_id = {$dateAttrId})"
                . " AND {$dateDefaultAlias}.store_id = 0",
                [
                    $attributeCode => new \Zend_Db_Expr(
                        "COALESCE({$attributeCode}.value, "
                        . "{$dateDefaultAlias}.value, {$dateChildrenAlias}.value)"
                    )
                ]
            )
            ->joinLeft(
                [$attributeCode => $tableAlias],
                "({$attributeCode}.{$linkFieldName} = e.{$linkFieldName})"
                . " AND ({$attributeCode}.attribute_id = {$dateAttrId})"
                . " AND {$attributeCode}.store_id = {$storeId}",
                []
            )
            // Join children values for configurable and bundle
            ->joinLeft(
                [$dateChildrenAlias => $tableAlias],
                "({$dateChildrenAlias}.{$linkFieldName} = {$childLinkAlias}.{$linkFieldName})"
                . " AND ({$dateChildrenAlias}.attribute_id = {$dateAttrId})"
                . " AND ({$dateChildrenAlias}.store_id = {$storeId}"
                . " OR {$dateChildrenAlias}.store_id = 0)",
                []
            )
            ->where(
                $connection->quoteInto(
                    '('
                    . '(COALESCE('. $attributeCode . '.value, '
                    . $dateDefaultAlias . '.value, '
                    . $dateChildrenAlias . '.value) IS NULL'
                    . ' OR COALESCE(' . $this->getDateFromAttrCode()
                    . '.value, ' . $dateDefaultAlias . '.value, '
                    . $dateChildrenAlias . '.value) ' . $condition . ' ?)'
                    . ')',
                    $value
                )
            );
    }

    /**
     * Join child products
     *
     * @param Collection $collection
     * @param bool|false $useLinkOnly
     * @return string
     * @throws \Exception
     */
    protected function joinChildProducts($collection, $useLinkOnly = false)
    {
        $childLinkName = 'child_link';
        $childProductEntityName = 'child_product_entity';
        $linkFieldName = $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();

        /** @var Select $select */
        $select = $collection->getSelect();

        $from = $select->getPart(\Zend_Db_Select::FROM);
        if (!isset($from[$childLinkName])) {
            $select->joinLeft(
                [$childLinkName => $collection->getTable('catalog_product_super_link')],
                '(' . $childLinkName . '.parent_id = e.' . $linkFieldName . ')',
                []
            );
        }

        if (!$useLinkOnly && !isset($from[$childProductEntityName])) {
            $additionalValidation = '';
            if ($linkFieldName == 'row_id') {
                $additionalValidation = ' AND ' . $childProductEntityName . '.created_in <= 1 AND '
                    . $childProductEntityName . '.updated_in > 1';
            }
            $select->joinLeft(
                [$childProductEntityName => $collection->getTable('catalog_product_entity')],
                '('
                . $childProductEntityName . '.entity_id = ' . $childLinkName . '.product_id'
                . $additionalValidation
                . ')',
                []
            );
        }

        $result = $useLinkOnly ? $childLinkName : $childProductEntityName;

        return $result;
    }

    /**
     * Retrieve attribute Id
     *
     * @param string $dateAttrCode
     * @return int
     */
    private function getDateAttrId($dateAttrCode)
    {
        if ($this->dateAttrIds === null || !isset($this->dateAttrIds[$dateAttrCode])) {
            $this->dateAttrIds[$dateAttrCode] = $this->resourceAttribute
                ->getIdByCode('catalog_product', $dateAttrCode);
        }
        return $this->dateAttrIds[$dateAttrCode];
    }
}
