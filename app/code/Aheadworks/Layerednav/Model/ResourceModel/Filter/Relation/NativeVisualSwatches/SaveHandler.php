<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\NativeVisualSwatches;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Product\Attribute\Swatch\Processor as ProductAttributeSwatchProcessor;
use Aheadworks\Layerednav\Model\Product\Attribute\Resolver as ProductAttributeResolver;
use Aheadworks\Layerednav\Api\Data\FilterExtensionInterface;
use Aheadworks\Layerednav\Model\Product\Attribute\Checker as ProductAttributeChecker;
use Aheadworks\Layerednav\Model\ResourceModel\FilterRepository;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\NativeVisualSwatches
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ProductAttributeSwatchProcessor
     */
    private $productAttributeSwatchProcessor;

    /**
     * @var ProductAttributeResolver
     */
    private $productAttributeResolver;

    /**
     * @var ProductAttributeChecker
     */
    private $productAttributeChecker;

    /**
     * @param ProductAttributeSwatchProcessor $productAttributeSwatchProcessor
     * @param ProductAttributeResolver $productAttributeResolver
     * @param ProductAttributeChecker $productAttributeChecker
     */
    public function __construct(
        ProductAttributeSwatchProcessor $productAttributeSwatchProcessor,
        ProductAttributeResolver $productAttributeResolver,
        ProductAttributeChecker $productAttributeChecker
    ) {
        $this->productAttributeSwatchProcessor = $productAttributeSwatchProcessor;
        $this->productAttributeResolver = $productAttributeResolver;
        $this->productAttributeChecker = $productAttributeChecker;
    }

    /**
     * @param FilterInterface $entity
     * @param array $arguments
     * @return FilterInterface
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if (isset($arguments[FilterRepository::IS_SYNCHRONIZATION_NEEDED_FLAG_NAME])
            && $arguments[FilterRepository::IS_SYNCHRONIZATION_NEEDED_FLAG_NAME]
        ) {
            $attribute = $this->productAttributeResolver->getProductAttributeByCode($entity->getCode());
            if ($attribute
                && $this->productAttributeChecker->areNativeVisualSwatchesUsed($attribute)
            ) {
                $this->productAttributeSwatchProcessor->setOptionsByFilter($attribute, $entity);
                $attribute->save();
            }
        }

        return $entity;
    }
}
