<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter;

use Aheadworks\Layerednav\Model\Product\Attribute\Resolver as ProductAttributeResolver;
use Aheadworks\Layerednav\Model\Product\Attribute\Checker as ProductAttributeChecker;
use Aheadworks\Layerednav\Model\Source\Filter\SwatchesMode as FilterSwatchesMode;

/**
 * Class Checker
 *
 * @package Aheadworks\Layerednav\Model\Filter
 */
class Checker
{
    /**
     * @var ProductAttributeResolver
     */
    private $productAttributeResolver;

    /**
     * @var ProductAttributeChecker
     */
    private $productAttributeChecker;

    /**
     * @param ProductAttributeResolver $productAttributeResolver
     * @param ProductAttributeChecker $productAttributeChecker
     */
    public function __construct(
        ProductAttributeResolver $productAttributeResolver,
        ProductAttributeChecker $productAttributeChecker
    ) {
        $this->productAttributeResolver = $productAttributeResolver;
        $this->productAttributeChecker = $productAttributeChecker;
    }

    /**
     * Check if swatches are allowed for attribute filter with specific code
     *
     * @param string $code
     * @return bool
     */
    public function areSwatchesAllowed($code)
    {
        $areSwatchesAllowed = false;

        $productAttribute = $this->productAttributeResolver->getProductAttributeByCode($code);
        if ($productAttribute) {
            $areSwatchesAllowed = $this->productAttributeChecker->areExtraSwatchesAllowed($productAttribute);
        }

        return $areSwatchesAllowed;
    }

    /**
     * Check if attribute with specified code uses native text swatches
     *
     * @param string $code
     * @return bool
     */
    public function areNativeTextSwatchesUsed($code)
    {
        $areTextSwatchesUsed = false;

        $productAttribute = $this->productAttributeResolver->getProductAttributeByCode($code);
        if ($productAttribute) {
            $areTextSwatchesUsed = $this->productAttributeChecker->areNativeTextSwatchesUsed($productAttribute);
        }

        return $areTextSwatchesUsed;
    }

    /**
     * Check if attribute with specified code uses native visual swatches
     *
     * @param string $code
     * @return bool
     */
    public function areNativeVisualSwatchesUsed($code)
    {
        $areNativeVisualSwatchesUsed = false;

        $productAttribute = $this->productAttributeResolver->getProductAttributeByCode($code);
        if ($productAttribute) {
            $areNativeVisualSwatchesUsed = $this->productAttributeChecker->areNativeVisualSwatchesUsed(
                $productAttribute
            );
        }

        return $areNativeVisualSwatchesUsed;
    }

    /**
     * Check if swatches view mode allows to display swatch image
     *
     * @param int $swatchesViewMode
     * @return bool
     */
    public function isNeedToShowSwatchImage($swatchesViewMode)
    {
        return in_array(
            $swatchesViewMode,
            [
                FilterSwatchesMode::IMAGE_AND_TITLE,
                FilterSwatchesMode::IMAGE_ONLY,
            ]
        );
    }

    /**
     * Check if swatches view mode allows to display swatch title
     *
     * @param int $swatchesViewMode
     * @return bool
     */
    public function isNeedToShowSwatchTitle($swatchesViewMode)
    {
        return in_array(
            $swatchesViewMode,
            [
                FilterSwatchesMode::IMAGE_AND_TITLE,
                FilterSwatchesMode::TITLE_ONLY,
            ]
        );
    }
}
