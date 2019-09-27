<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item;

/**
 * Class DataBuilderPool
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item
 */
class DataBuilderPool
{
    /**
     * @var array
     */
    private $dataBuilders = [];

    /**
     * @var DataBuilderInterface[]
     */
    private $dataBuilderInstances = [];

    /**
     * @var DataBuilderFactory
     */
    private $dataBuilderFactory;

    /**
     * @param DataBuilderFactory $dataBuilderFactory
     * @param array $dataBuilders
     */
    public function __construct(
        DataBuilderFactory $dataBuilderFactory,
        $dataBuilders = []
    ) {
        $this->dataBuilderFactory = $dataBuilderFactory;
        $this->dataBuilders = $dataBuilders;
    }

    /**
     * Retrieve item data builder instance of given type
     *
     * @param string $type
     * @return DataBuilderInterface
     * @throws \Exception
     */
    public function getDataBuilder($type)
    {
        if (!isset($this->dataBuilderInstances[$type])) {
            if (!isset($this->dataBuilders[$type])) {
                throw new \Exception(sprintf('Unknown data builder type: %s requested', $type));
            }
            $builderInstance = $this->dataBuilderFactory->create($this->dataBuilders[$type]);
            if (!$builderInstance instanceof DataBuilderInterface) {
                throw new \Exception(
                    sprintf('Data builder instance %s does not implement required interface.', $type)
                );
            }
            $this->dataBuilderInstances[$type] = $builderInstance;
        }
        return $this->dataBuilderInstances[$type];
    }
}
