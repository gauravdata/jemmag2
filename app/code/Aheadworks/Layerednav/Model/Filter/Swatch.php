<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter;

use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class Swatch
 *
 * @package Aheadworks\Layerednav\Model\Filter
 */
class Swatch extends AbstractExtensibleModel implements SwatchInterface
{
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
    public function getFilterId()
    {
        return $this->getData(self::FILTER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setFilterId($filterId)
    {
        return $this->setData(self::FILTER_ID, $filterId);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsDefault()
    {
        return $this->getData(self::IS_DEFAULT);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsDefault($isDefault)
    {
        return $this->setData(self::IS_DEFAULT, $isDefault);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
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
    public function getOptionId()
    {
        return $this->getData(self::OPTION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptionId($optionId)
    {
        return $this->setData(self::OPTION_ID, $optionId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStorefrontTitle()
    {
        return $this->getData(self::CURRENT_STOREFRONT_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentStorefrontTitle($currentStorefrontTitle)
    {
        return $this->setData(self::CURRENT_STOREFRONT_TITLE, $currentStorefrontTitle);
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
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\Layerednav\Api\Data\Filter\SwatchExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
