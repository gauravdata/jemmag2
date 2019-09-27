<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches;

use Magento\Swatches\Model\Swatch;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\Attribute\Option as AttributeOption;
use Magento\Swatches\Block\LayeredNavigation\RenderLayered as SwatchesRenderLayeredBlock;
use Magento\Framework\Filter\FilterManager;
use Aheadworks\Layerednav\Model\Seo\Checker as SeoChecker;

/**
 * Class Resolver
 *
 * @package Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches
 */
class Resolver
{
    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @var SeoChecker
     */
    private $seoChecker;

    /**
     * @param FilterManager $filterManager
     * @param SeoChecker $seoChecker
     */
    public function __construct(
        FilterManager $filterManager,
        SeoChecker $seoChecker
    ) {
        $this->filterManager = $filterManager;
        $this->seoChecker = $seoChecker;
    }

    /**
     * Check if need to display value for swatches option
     *
     * @param string $swatchesType
     * @return bool
     */
    public function isNeedToDisplayOptionSwatchesValue($swatchesType)
    {
        return (
            !in_array(
                (int)$swatchesType,
                [
                    Swatch::SWATCH_TYPE_VISUAL_COLOR,
                    Swatch::SWATCH_TYPE_VISUAL_IMAGE,
                    Swatch::SWATCH_TYPE_EMPTY,
                ]
            )
        );
    }

    /**
     * Retrieve option value
     *
     * @param AttributeOption $option
     * @return string
     */
    public function getOptionValue($option)
    {
        return $this->isNeedToReplaceOptionValueByText()
            ? $this->filterManager->translitUrl(urlencode($option->getLabel()))
            : $option->getValue();
    }

    /**
     * Check if option value should be replaced by url compatible text representation
     *
     * @return bool
     */
    public function isNeedToReplaceOptionValueByText()
    {
        return $this->seoChecker->isNeedToUseTextValues();
    }

    /**
     * Check if need to show empty results
     *
     * @param Attribute $attributeModel
     * @return bool
     */
    public function isNeedToShowEmptyResults($attributeModel)
    {
        return $attributeModel->getIsFilterable() != SwatchesRenderLayeredBlock::FILTERABLE_WITH_RESULTS;
    }
}
