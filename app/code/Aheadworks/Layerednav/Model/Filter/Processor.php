<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter;

use Aheadworks\Layerednav\Model\Product\Attribute\Resolver as AttributeResolver;
use Aheadworks\Layerednav\Api\Data\FilterExtensionInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Aheadworks\Layerednav\Model\Product\Attribute\Checker as ProductAttributeChecker;

/**
 * Class Processor
 *
 * @package Aheadworks\Layerednav\Model\Filter
 */
class Processor
{
    /**
     * @var AttributeResolver
     */
    private $attributeResolver;

    /**
     * @var ProductAttributeChecker
     */
    private $productAttributeChecker;

    /**
     * @param AttributeResolver $attributeResolver
     * @param ProductAttributeChecker $productAttributeChecker
     */
    public function __construct(
        AttributeResolver $attributeResolver,
        ProductAttributeChecker $productAttributeChecker
    ) {
        $this->attributeResolver = $attributeResolver;
        $this->productAttributeChecker = $productAttributeChecker;
    }

    /**
     * Set swatches to the filter based on the options of specific attribute
     *
     * @param FilterInterface $filter
     * @param ProductAttributeInterface $attribute
     * @return FilterInterface
     */
    public function setSwatchesByAttribute($filter, $attribute)
    {
        if ($filter->getCode() == $attribute->getAttributeCode()
            && $this->productAttributeChecker->areExtraSwatchesAllowed($attribute)
        ) {
            $swatches = $this->attributeResolver->getFilterSwatches($attribute);
            /** @var FilterExtensionInterface $extensionAttributes */
            $extensionAttributes = $filter->getExtensionAttributes();
            $extensionAttributes->setSwatches($swatches);
            $filter->setExtensionAttributes($extensionAttributes);
        }

        return $filter;
    }

    /**
     * Set native visual swatches to the filter based on the options of specific attribute
     *
     * @param FilterInterface $filter
     * @param ProductAttributeInterface $attribute
     * @return FilterInterface
     */
    public function setNativeVisualSwatchesByAttribute($filter, $attribute)
    {
        if ($filter->getCode() == $attribute->getAttributeCode()
            && $this->productAttributeChecker->areNativeVisualSwatchesUsed($attribute)
        ) {
            $swatches = $this->attributeResolver->getFilterSwatches($attribute);
            /** @var FilterExtensionInterface $extensionAttributes */
            $extensionAttributes = $filter->getExtensionAttributes();
            $extensionAttributes->setNativeVisualSwatches($swatches);
            $filter->setExtensionAttributes($extensionAttributes);
        }
        return $filter;
    }
}
