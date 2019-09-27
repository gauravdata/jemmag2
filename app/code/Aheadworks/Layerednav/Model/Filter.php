<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\FilterExtensionInterface;
use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Filter as FilterResource;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class Filter
 *
 * @method FilterCategoryInterface getCategoryFilterData()
 * @method $this setCategoryFilterData(FilterCategoryInterface $categoryFilterData)
 *
 * @package Aheadworks\Layerednav\Model
 * @codeCoverageIgnore
 */
class Filter extends AbstractExtensibleModel implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(FilterResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultTitle()
    {
        return $this->getData(self::DEFAULT_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultTitle($defaultTitle)
    {
        return $this->setData(self::DEFAULT_TITLE, $defaultTitle);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsFilterable()
    {
        return $this->getData(self::IS_FILTERABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsFilterable($isFilterable)
    {
        return $this->setData(self::IS_FILTERABLE, $isFilterable);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsFilterableInSearch()
    {
        return $this->getData(self::IS_FILTERABLE_IN_SEARCH);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsFilterableInSearch($isFilterableInSearch)
    {
        return $this->setData(self::IS_FILTERABLE_IN_SEARCH, $isFilterableInSearch);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * {@inheritdoc}
     */
    public function getStorefrontTitle()
    {
        return $this->getData(self::STOREFRONT_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStorefrontTitle($storefrontTitle)
    {
        return $this->setData(self::STOREFRONT_TITLE, $storefrontTitle);
    }

    /**
     * {@inheritdoc}
     */
    public function getStorefrontTitles()
    {
        return $this->getData(self::STOREFRONT_TITLES);
    }

    /**
     * {@inheritdoc}
     */
    public function setStorefrontTitles($titles = [])
    {
        return $this->setData(self::STOREFRONT_TITLES, $titles);
    }

    /**
     * {@inheritdoc}
     */
    public function getStorefrontDisplayState()
    {
        return $this->getData(self::STOREFRONT_DISPLAY_STATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStorefrontDisplayState($storefrontDisplayState)
    {
        return $this->setData(self::STOREFRONT_DISPLAY_STATE, $storefrontDisplayState);
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayStates()
    {
        return $this->getData(self::DISPLAY_STATES);
    }

    /**
     * {@inheritdoc}
     */
    public function setDisplayStates($displayStates)
    {
        return $this->setData(self::DISPLAY_STATES, $displayStates);
    }

    /**
     * {@inheritdoc}
     */
    public function getStorefrontSortOrder()
    {
        return $this->getData(self::STOREFRONT_SORT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setStorefrontSortOrder($storefrontSortOrder)
    {
        return $this->setData(self::STOREFRONT_SORT_ORDER, $storefrontSortOrder);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrders()
    {
        return $this->getData(self::SORT_ORDERS);
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrders($sortOrders)
    {
        return $this->setData(self::SORT_ORDERS, $sortOrders);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryMode()
    {
        return $this->getData(self::CATEGORY_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryMode($categoryMode)
    {
        return $this->setData(self::CATEGORY_MODE, $categoryMode);
    }

    /**
     * {@inheritdoc}
     */
    public function getExcludeCategoryIds()
    {
        return $this->getData(self::EXCLUDE_CATEGORY_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setExcludeCategoryIds($excludeCategoryIds)
    {
        return $this->setData(self::EXCLUDE_CATEGORY_IDS, $excludeCategoryIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * {@inheritdoc}
     */
    public function getImageStorefrontTitle()
    {
        return $this->getData(self::IMAGE_STOREFRONT_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setImageStorefrontTitle($imageStorefrontTitle)
    {
        return $this->setData(self::IMAGE_STOREFRONT_TITLE, $imageStorefrontTitle);
    }

    /**
     * {@inheritdoc}
     */
    public function getImageTitles()
    {
        return $this->getData(self::IMAGE_TITLES);
    }

    /**
     * {@inheritdoc}
     */
    public function setImageTitles($imageTitles = [])
    {
        return $this->setData(self::IMAGE_TITLES, $imageTitles);
    }

    /**
     * {@inheritdoc}
     */
    public function getSwatchesViewMode()
    {
        return $this->getData(self::SWATCHES_VIEW_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSwatchesViewMode($swatchesViewMode)
    {
        return $this->setData(self::SWATCHES_VIEW_MODE, $swatchesViewMode);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(FilterExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
