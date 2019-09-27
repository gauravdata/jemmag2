<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter;

use Aheadworks\Layerednav\Model\Layer\Filter;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;

/**
 * Class Item
 * @package Aheadworks\Layerednav\Model\Layer\Filter
 * @codeCoverageIgnore
 */
class Item implements FilterItemInterface
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $value;

    /**
     * @var int
     */
    private $count;

    /**
     * @var array
     */
    private $imageData;

    /**
     * @param FilterInterface $filter
     * @param string $label
     * @param string $value
     * @param string $count
     * @param array $imageData
     */
    public function __construct(
        FilterInterface $filter,
        $label,
        $value,
        $count,
        $imageData = []
    ) {
        $this->filter = $filter;
        $this->label = $label;
        $this->value = $value;
        $this->count = $count;
        $this->imageData = $imageData;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function getImageData()
    {
        return $this->imageData;
    }
}
