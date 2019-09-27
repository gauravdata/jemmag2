<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter;

use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection as FulltextCollection;
use Magento\Framework\Exception\StateException;

/**
 * Class DataResolver
 * @package Aheadworks\Layerednav\Model\Layer\Filter
 */
class DataResolver
{
    /**
     * Get faceted data
     *
     * @param FilterInterface $filter
     * @param string|null $code
     * @return array ['<key>' => ['value' => '<key>', 'count' => 0], ...]
     */
    public function getFacetedData($filter, $code = null)
    {
        if ($code === null) {
            $attribute = $filter->getAttributeModel();
            $code = $attribute->getAttributeCode();
        }

        /** @var FulltextCollection $productCollection */
        $productCollection = $filter->getLayer()->getProductCollection();
        try {
            $facets = $productCollection->getFacetedData($code);
        } catch (StateException $e) {
            $facets = [];
        }

        return $facets;
    }
}
