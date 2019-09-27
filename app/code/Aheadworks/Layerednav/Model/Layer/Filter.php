<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\ProviderInterface as ItemsProviderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class Filter
 * @package Aheadworks\Layerednav\Model\Layer
 */
class Filter extends AbstractSimpleObject implements FilterInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const CODE              = 'code';
    const TITLE             = 'title';
    const TYPE              = 'type';
    const IMAGE             = 'image';
    const LAYER             = 'layer';
    const ATTRIBUTE         = 'attribute';
    const ADDITIONAL_DATA   = 'additional_data';
    /**#@-*/

    /**
     * @var ItemsProviderInterface
     */
    private $itemsProvider;

    /**
     * @var FilterItemInterface[]
     */
    private $items;

    /**
     * @param ItemsProviderInterface $itemsProvider
     * @param array $data
     */
    public function __construct(
        ItemsProviderInterface $itemsProvider,
        array $data = []
    ) {
        $this->itemsProvider = $itemsProvider;
        parent::__construct($data);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCode()
    {
        return $this->_get(self::CODE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getImage()
    {
        return $this->_get(self::IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        if ($this->items === null) {
            $this->items = $this->itemsProvider->getItems($this);
        }

        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsCount()
    {
        return count($this->getItems());
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getLayer()
    {
        return $this->_get(self::LAYER);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getAttributeModel()
    {
        return $this->_get(self::ATTRIBUTE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getItemsProvider()
    {
        return $this->itemsProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalData($name)
    {
        $additionalData = $this->_get(self::ADDITIONAL_DATA);
        if ($additionalData && isset($additionalData[$name])) {
            return $additionalData[$name];
        }

        return null;
    }
}
