<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Product\Collection;

use Aheadworks\Layerednav\Model\DateResolver;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as ResourceAttribute;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class NewProvider
 * @package Aheadworks\Layerednav\Model\ResourceModel\Product\Collection
 * @codeCoverageIgnore
 */
class NewProvider extends AbstractProvider
{
    const DATE_TO_ATTRIBUTE_CODE = 'news_to_date';
    const DATE_FROM_ATTRIBUTE_CODE = 'news_from_date';

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
        parent::__construct(
            $metadataPool,
            $resourceAttribute,
            $dateResolver,
            $productCollectionFactory,
            $productVisibility
        );
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
    public function getProductCollection($withVisibility = false, $storeId = null)
    {
        $collection = $this->getBaseProductCollection($withVisibility, $storeId);

        $from = $this->dateResolver->getDateInDbFormat(false, 0, 0, 0);
        $to = $this->dateResolver->getDateInDbFormat(false, 23, 59, 59);

        $this->addDateFilter($collection, $this->getDateFromAttrCode(), $to, '<=', $storeId, false);
        $this->addDateFilter($collection, $this->getDateToAttrCode(), $from, '>=', $storeId, false);

        $collection
            ->getSelect()
            ->where(
                '('
                . 'COALESCE(' . $this->getDateFromAttrCode(). '.value, '
                . $this->getDateFromAttrCode() . '_default.value, '
                . $this->getDateFromAttrCode() . '_children.value) IS NOT NULL'
                . ' OR COALESCE(' . $this->getDateToAttrCode() . '.value, '
                . $this->getDateToAttrCode(). '_default.value, '
                . $this->getDateToAttrCode(). '_children.value) IS NOT NULL'
                . ')'
            )
            ->group('e.entity_id');

        return $collection;
    }

    /**
     *
     * Get product ids
     *
     * @param bool|false $withVisibility
     * @param int|null $storeId
     * @return int[]
     */
    public function getProductIds($withVisibility = false, $storeId = null)
    {
        $collection = $this->getProductCollection($withVisibility, $storeId);

        return $collection->getAllIds();
    }
}
