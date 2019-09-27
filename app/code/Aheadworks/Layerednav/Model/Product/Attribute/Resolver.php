<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Product\Attribute;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Layerednav\Model\Product\Attribute\Option\Resolver as AttributeOptionResolver;
use Aheadworks\Layerednav\Model\Product\Attribute\Option\Converter as AttributeOptionConverter;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Magento\Backend\Model\UrlInterface as BackendUrlInterface;

/**
 * Class Resolver
 *
 * @package Aheadworks\Layerednav\Model\Product\Attribute
 */
class Resolver
{
    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * @var AttributeOptionResolver
     */
    private $attributeOptionResolver;

    /**
     * @var AttributeOptionConverter
     */
    private $attributeOptionConverter;

    /**
     * @var BackendUrlInterface
     */
    private $backendUrlBuilder;

    /**
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param AttributeOptionResolver $attributeOptionResolver
     * @param AttributeOptionConverter $attributeOptionConverter
     * @param BackendUrlInterface $backendUrlBuilder
     */
    public function __construct(
        ProductAttributeRepositoryInterface $productAttributeRepository,
        AttributeOptionResolver $attributeOptionResolver,
        AttributeOptionConverter $attributeOptionConverter,
        BackendUrlInterface $backendUrlBuilder
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->attributeOptionResolver = $attributeOptionResolver;
        $this->attributeOptionConverter = $attributeOptionConverter;
        $this->backendUrlBuilder = $backendUrlBuilder;
    }

    /**
     * Retrieve product attribute by its code
     *
     * @param string $attributeCode
     * @return ProductAttributeInterface|null
     */
    public function getProductAttributeByCode($attributeCode)
    {
        try {
            $productAttribute = $this->productAttributeRepository->get($attributeCode);
        } catch (NoSuchEntityException $exception) {
            $productAttribute = null;
        }

        return $productAttribute;
    }

    /**
     * Retrieve filter swatches from specific attribute and its options
     *
     * @param ProductAttributeInterface $attribute
     * @return SwatchInterface[]
     */
    public function getFilterSwatches($attribute)
    {
        $filterSwatches = [];
        $attributeOptions = $this->attributeOptionResolver->getByAttribute($attribute);
        foreach ($attributeOptions as $option) {
            $swatchItem = $this->attributeOptionConverter->toFilterSwatchItem($attribute, $option);
            $filterSwatches[] = $swatchItem;
        }

        return $filterSwatches;
    }

    /**
     * Retrieve link to edit product attribute in the backend
     *
     * @param string $attributeCode
     * @return string
     */
    public function getBackendEditLinkByCode($attributeCode)
    {
        $link = '';
        $productAttribute = $this->getProductAttributeByCode($attributeCode);
        if ($productAttribute) {
            $link = $this->backendUrlBuilder->getUrl(
                'catalog/product_attribute/edit',
                [
                    'attribute_id' => $productAttribute->getAttributeId(),
                ]
            );
        }
        return $link;
    }
}
