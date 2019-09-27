<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer;

use Aheadworks\Layerednav\Model\Layer\Filter\Checker as FilterChecker;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\Checker as FilterItemChecker;
use Aheadworks\Layerednav\Model\Image\ViewInterface as ImageViewInterface;
use Magento\Swatches\Model\Swatch;

/**
 * Class DefaultRenderer
 *
 * @package Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer
 */
class DefaultRenderer extends Base
{
    /**
     * @var FilterChecker
     */
    private $filterChecker;

    /**
     * @param Config $config
     * @param FilterItemChecker $filterItemChecker
     * @param FilterChecker $filterChecker
     */
    public function __construct(
        Config $config,
        FilterItemChecker $filterItemChecker,
        FilterChecker $filterChecker
    ) {
        parent::__construct($config, $filterItemChecker);
        $this->filterChecker = $filterChecker;
    }

    /**
     * Retrieve filter item value
     *
     * @param FilterItemInterface $filterItem
     * @return string
     */
    public function getFilterItemValue($filterItem)
    {
        return $filterItem->getValue();
    }

    /**
     * Retrieve html id for filter item input
     *
     * @param FilterItemInterface $filterItem
     * @return string
     */
    public function getInputId($filterItem)
    {
        return 'aw-filter-option-'
            . $filterItem->getFilter()->getCode()
            . '-'
            . $this->getFilterItemValue($filterItem);
    }

    /**
     * Get attribute filter backend type. Returns '' for non-attribute filter items
     *
     * @param FilterItemInterface $item
     * @return string
     */
    public function getBackendType(FilterItemInterface $item)
    {
        $filter = $item->getFilter();
        $attributeModel = $filter->getAttributeModel();
        $backendType = $attributeModel ? $attributeModel->getBackendType() : '';

        return $backendType;
    }

    /**
     * Check if need to show products count in the parentheses
     *
     * @param FilterItemInterface $item
     * @return bool
     */
    public function isNeedToShowProductsCount(FilterItemInterface $item)
    {
        return $this->config->isNeedToShowProductsCount()
            && (!$this->isActiveItem($item))
            && $item->getCount();
    }

    /**
     * Check if multi select available
     *
     * @param FilterItemInterface $filterItem
     * @return bool
     */
    public function isMultiselectAvailable(FilterItemInterface $filterItem)
    {
        /** @var FilterInterface $filter */
        $filter = $filterItem->getFilter();

        return $this->filterChecker->isMultiselectAvailable($filter);
    }

    /**
     * Check if item is disabled
     *
     * @param FilterItemInterface $filterItem
     * @return bool
     */
    public function isItemDisabled($filterItem)
    {
        return !$filterItem->getCount() && !$this->isActiveItem($filterItem);
    }

    /**
     * Check if need to show filter item swatch image
     *
     * @param FilterInterface $filter
     * @return bool
     */
    public function isNeedToShowFilterItemImage($filter)
    {
        return $this->filterChecker->isNeedToShowFilterItemImage($filter);
    }

    /**
     * Check if need to show filter item label
     *
     * @param FilterInterface $filter
     * @return bool
     */
    public function isNeedToShowFilterItemLabel($filter)
    {
        return $this->filterChecker->isNeedToShowFilterItemLabel($filter);
    }

    /**
     * Retrieve custom class string for filter item swatch image
     *
     * @param FilterItemInterface $filterItem
     * @return string
     */
    public function getFilterItemImageCustomClass($filterItem)
    {
        $customClass = '';
        if (!$this->isColorUsedForImage($filterItem) && !$this->isUrlUsedForImage($filterItem)) {
            $customClass .= ' empty';
        }
        if ($this->isColorUsedForImage($filterItem)) {
            $customClass .= ' color';
        }
        return $customClass;
    }

    /**
     * Retrieve custom style string for filter item swatch image
     *
     * @param FilterItemInterface $filterItem
     * @return string
     */
    public function getFilterItemImageCustomStyle($filterItem)
    {
        $customStyle = "";
        if ($this->isColorUsedForImage($filterItem)) {
            $customStyle = "background: " . $this->getFilterItemImageColor($filterItem);
        } elseif ($this->isUrlUsedForImage($filterItem)) {
            $customStyle = "";
        }
        return $customStyle;
    }

    /**
     * Retrieve url for filter item swatch image
     *
     * @param FilterItemInterface $filterItem
     * @return string
     */
    public function getFilterItemImageUrl($filterItem)
    {
        $imageData = $filterItem->getImageData();
        return isset($imageData[ImageViewInterface::URL]) ? $imageData[ImageViewInterface::URL] : '';
    }

    /**
     * Retrieve color for filter item swatch image
     *
     * @param FilterItemInterface $filterItem
     * @return string
     */
    public function getFilterItemImageColor($filterItem)
    {
        $imageData = $filterItem->getImageData();
        return isset($imageData['color']) ? $imageData['color'] : '';
    }

    /**
     * Retrieve option type for tooltip rendering
     *
     * @param FilterItemInterface $filterItem
     * @return int
     */
    public function getTooltipOptionType($filterItem)
    {
        $type = Swatch::SWATCH_TYPE_TEXTUAL;
        if ($this->isColorUsedForImage($filterItem)) {
            $type = Swatch::SWATCH_TYPE_VISUAL_COLOR;
        } elseif ($this->isUrlUsedForImage($filterItem)) {
            $type = Swatch::SWATCH_TYPE_VISUAL_IMAGE;
        }
        return $type;
    }

    /**
     * Retrieve option thumb for tooltip rendering
     *
     * @param FilterItemInterface $filterItem
     * @return string
     */
    public function getTooltipOptionThumb($filterItem)
    {
        $thumb = '';
        if ($this->isUrlUsedForImage($filterItem)) {
            $thumb = $this->getFilterItemImageUrl($filterItem);
        }
        return $thumb;
    }

    /**
     * Retrieve option value for tooltip rendering
     *
     * @param FilterItemInterface $filterItem
     * @return string
     */
    public function getTooltipOptionValue($filterItem)
    {
        $value = '';
        if ($this->isColorUsedForImage($filterItem)) {
            $value = $this->getFilterItemImageColor($filterItem);
        }
        return $value;
    }

    /**
     * Check if color is used to display image
     *
     * @param FilterItemInterface $filterItem
     * @return bool
     */
    private function isColorUsedForImage($filterItem)
    {
        $imageData = $filterItem->getImageData();
        return isset($imageData['color']);
    }

    /**
     * Check if url is used to display image
     *
     * @param FilterItemInterface $filterItem
     * @return bool
     */
    private function isUrlUsedForImage($filterItem)
    {
        $imageData = $filterItem->getImageData();
        return isset($imageData[ImageViewInterface::URL]);
    }
}
