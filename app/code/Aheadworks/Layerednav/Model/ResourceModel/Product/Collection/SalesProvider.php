<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Product\Collection;

use Aheadworks\Layerednav\Model\DateResolver;
use Aheadworks\Layerednav\Model\Store\Resolver as StoreResolver;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as ResourceAttribute;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class SalesProvider
 * @package Aheadworks\Layerednav\Model\ResourceModel\Product\Collection
 * @codeCoverageIgnore
 */
class SalesProvider extends AbstractProvider
{
    const DATE_TO_ATTRIBUTE_CODE = 'special_to_date';
    const DATE_FROM_ATTRIBUTE_CODE = 'special_from_date';
    const SPECIAL_PRICE_ATTRIBUTE_CODE = 'special_price';

    /**
     * @var StoreResolver
     */
    private $storeResolver;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceAttribute $resourceAttribute
     * @param DateResolver $dateResolver
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ProductVisibility $productVisibility
     * @param StoreResolver $storeResolver
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceAttribute $resourceAttribute,
        DateResolver $dateResolver,
        ProductCollectionFactory $productCollectionFactory,
        ProductVisibility $productVisibility,
        StoreResolver $storeResolver
    ) {
        parent::__construct(
            $metadataPool,
            $resourceAttribute,
            $dateResolver,
            $productCollectionFactory,
            $productVisibility
        );
        $this->storeResolver = $storeResolver;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDateFromAttrCode()
    {
        return self::DATE_FROM_ATTRIBUTE_CODE;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDateToAttrCode()
    {
        return self::DATE_TO_ATTRIBUTE_CODE;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductCollection($withVisibility = false, $customerGroupId = null, $storeId = null)
    {
        $collection = $this->getBaseProductCollection($withVisibility, $storeId);
        $websiteId = $this->storeResolver->getWebsiteIdByStoreId($storeId);
        $customerGroupId = $customerGroupId !== null ? $customerGroupId : 0;

        $collection->addPriceData($customerGroupId, $websiteId);
        $this->addOnSaleFilter($collection, $customerGroupId, $websiteId);

        return $collection;
    }

    /**
     * Get product ids
     *
     * @param bool|false $withVisibility
     * @param int|null $customerGroupId
     * @param int|null $storeId
     * @return int[]
     * @throws \Exception
     */
    public function getProductIds($withVisibility = false, $customerGroupId = null, $storeId = null)
    {
        $collection = $this->getProductCollection($withVisibility, $customerGroupId, $storeId);

        return $collection->getAllIds();
    }

    /**
     * Add on sale filter
     *
     * @param Collection $collection
     * @param $customerGroupId
     * @param $websiteId
     * @throws \Exception
     */
    private function addOnSaleFilter($collection, $customerGroupId, $websiteId)
    {
        $childLinkAlias = $this->joinChildProducts($collection, true);

        $collection
            ->getSelect()
            ->joinLeft(
                ['children_price_index' => $collection->getTable('catalog_product_index_price')],
                '(children_price_index.entity_id = ' . $childLinkAlias . '.product_id'
                . ' AND children_price_index.website_id = ' . $websiteId
                . ' AND children_price_index.customer_group_id = ' . $customerGroupId
                . ' AND children_price_index.final_price < children_price_index.price)',
                []
            )
            ->where(
                '(' . $childLinkAlias . '.parent_id IS NOT NULL AND children_price_index.final_price IS NOT NULL)'
                . ' OR '
                . '(' . $childLinkAlias . '.parent_id IS NULL AND price_index.final_price < price_index.price)'
            )
            ->group('e.entity_id');
    }
}
