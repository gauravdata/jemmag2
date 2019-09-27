<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item;

/**
 * Class AbstractDataBuilder
 *
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item
 */
abstract class AbstractDataBuilder implements DataBuilderInterface
{
    /**
     * Array of items data
     * array(
     *      $index => array(
     *          'label'     => $label,
     *          'value'     => $value,
     *          'count'     => $count,
     *          'imageData' => $imageData,
     *      )
     * )
     *
     * @return array
     */
    protected $_itemsData = [];

    /**
     * {@inheritdoc}
     */
    public function addItemData($label, $value, $count, $imageData = [])
    {
        $this->_itemsData[] = [
            'label'     => $label,
            'value'     => $value,
            'count'     => $count,
            'imageData' => $imageData,
        ];
    }

    /**
     * {@inheritdoc}
     */
    abstract public function build();
}
