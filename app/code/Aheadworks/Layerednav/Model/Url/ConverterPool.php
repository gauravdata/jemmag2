<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Url;

/**
 * Class ConverterPool
 * @package Aheadworks\Layerednav\Model\Url
 */
class ConverterPool
{
    /**
     * @var array
     */
    private $converters = [];

    /**
     * @var ConverterInterface[]
     */
    private $converterInstances = [];

    /**
     * @var ConverterFactory
     */
    private $converterFactory;

    /**
     * @param ConverterFactory $converterFactory
     * @param array $converters
     */
    public function __construct(
        ConverterFactory $converterFactory,
        $converters = []
    ) {
        $this->converterFactory = $converterFactory;
        $this->converters = $converters;
    }

    /**
     * Retrieve converter instance
     *
     * @param string $fromType
     * @param string $toType
     * @return ConverterInterface
     * @throws \Exception
     */
    public function getConverter($fromType, $toType)
    {
        $key = $fromType . '-' . $toType;
        if (!isset($this->converterInstances[$key])) {
            if (!isset($this->converters[$fromType][$toType])) {
                throw new \Exception(
                    sprintf('Unknown convert type: from %s to %s  requested', $fromType, $toType)
                );
            }
            $converterInstance = $this->converterFactory->create($this->converters[$fromType][$toType]);
            if (!$converterInstance instanceof ConverterInterface) {
                throw new \Exception(
                    sprintf('From %s to %s converter does not implement required interface.', $fromType, $toType)
                );
            }
            $this->converterInstances[$key] = $converterInstance;
        }
        return $this->converterInstances[$key];
    }
}
