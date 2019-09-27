<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\NativeVisualSwatches;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Layerednav\Api\Data\FilterExtensionInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Product\Attribute\Resolver as ProductAttributeResolver;
use Aheadworks\Layerednav\Model\Filter\Processor as FilterProcessor;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\NativeVisualSwatches
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var ProductAttributeResolver
     */
    private $productAttributeResolver;

    /**
     * @var FilterProcessor
     */
    private $filterProcessor;

    /**
     * @param ProductAttributeResolver $productAttributeResolver
     * @param FilterProcessor $filterProcessor
     */
    public function __construct(
        ProductAttributeResolver $productAttributeResolver,
        FilterProcessor $filterProcessor
    ) {
        $this->productAttributeResolver = $productAttributeResolver;
        $this->filterProcessor = $filterProcessor;
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
            $attribute = $this->productAttributeResolver->getProductAttributeByCode($entity->getCode());
            if ($attribute) {
                $this->filterProcessor->setNativeVisualSwatchesByAttribute($entity, $attribute);
            }
        }
        return $entity;
    }
}
