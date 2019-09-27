<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Product\Attribute;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Swatches\Model\SwatchAttributeType;

/**
 * Class Checker
 *
 * @package Aheadworks\Layerednav\Model\Product\Attribute
 */
class Checker
{
    /**
     * @var array
     */
    private $inputTypesAllowedForSwatches = [];

    /**
     * @var SwatchAttributeType
     */
    private $swatchAttributeType;

    /**
     * @param SwatchAttributeType $swatchAttributeType
     * @param array $inputTypesAllowedForSwatches
     */
    public function __construct(
        SwatchAttributeType $swatchAttributeType,
        array $inputTypesAllowedForSwatches = []
    ) {
        $this->swatchAttributeType = $swatchAttributeType;
        $this->inputTypesAllowedForSwatches = $inputTypesAllowedForSwatches;
    }

    /**
     * Check if attribute uses specific source model
     *
     * @param AbstractAttribute $attribute
     * @return bool
     */
    public function isSourceModelUsed($attribute)
    {
        return !$attribute->getCanManageOptionLabels() &&
            !$attribute->getIsUserDefined() &&
            $attribute->getSourceModel();
    }

    /**
     * Check if additional swatches are allowed for attribute
     *
     * @param AbstractAttribute $attribute
     * @return bool
     */
    public function areExtraSwatchesAllowed($attribute)
    {
        $frontendInputType = $attribute->getFrontendInput();
        $isInputTypeAllowsSwatches = in_array($frontendInputType, $this->inputTypesAllowedForSwatches);
        $areNativeTextSwatchesUsed = $this->areNativeTextSwatchesUsed($attribute);
        $areNativeVisualSwatchesUsed = $this->areNativeVisualSwatchesUsed($attribute);
        $areSwatchesAllowed = $isInputTypeAllowsSwatches
            && !$areNativeTextSwatchesUsed
            && !$areNativeVisualSwatchesUsed
        ;
        return $areSwatchesAllowed;
    }

    /**
     * Check if attribute uses native text swatches
     *
     * @param AbstractAttribute $attribute
     * @return bool
     */
    public function areNativeTextSwatchesUsed($attribute)
    {
        return $this->swatchAttributeType->isTextSwatch($attribute);
    }

    /**
     * Check if attribute uses native visual swatches
     *
     * @param AbstractAttribute $attribute
     * @return bool
     */
    public function areNativeVisualSwatchesUsed($attribute)
    {
        return $this->swatchAttributeType->isVisualSwatch($attribute);
    }
}
