<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Search\RequestBuilder;

use Magento\Framework\Search\Request\Dimension;
use Magento\Framework\Search\Request\DimensionFactory;
use Magento\Framework\Search\Request\Mapper;
use Magento\Framework\Search\Request\MapperFactory;
use Magento\Framework\Search\RequestFactory;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Exception\StateException;

/**
 * Class Converter
 * @package Aheadworks\Layerednav\Model\Search\Search\RequestBuilder
 */
class Converter
{
    /**
     * @var MapperFactory
     */
    private $mapperFactory;

    /**
     * @var DimensionFactory
     */
    private $dimensionFactory;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @param MapperFactory $mapperFactory
     * @param DimensionFactory $dimensionFactory
     * @param RequestFactory $requestFactory
     */
    public function __construct(
        MapperFactory $mapperFactory,
        DimensionFactory $dimensionFactory,
        RequestFactory $requestFactory
    ) {
        $this->mapperFactory = $mapperFactory;
        $this->dimensionFactory = $dimensionFactory;
        $this->requestFactory = $requestFactory;
    }

    /**
     * Convert array to Request instance
     *
     * @param array $data
     * @return RequestInterface
     * @throws StateException
     */
    public function convert($data)
    {
        /** @var Mapper $mapper */
        $mapper = $this->mapperFactory->create(
            [
                'rootQueryName' => $data['query'],
                'queries' => $data['queries'],
                'aggregations' => $data['aggregations'],
                'filters' => $data['filters']
            ]
        );
        $requestData = [
            'name' => $data['query'],
            'indexName' => $data['index'],
            'from' => $data['from'],
            'size' => $data['size'],
            'query' => $mapper->getRootQuery(),
            'dimensions' => $this->buildDimensions(isset($data['dimensions']) ? $data['dimensions'] : []),
            'buckets' => $mapper->getBuckets(),
        ];
        if (isset($data['sort'])) {
            $requestData['sort'] = $data['sort'];
        }
        return $this->requestFactory->create($requestData);
    }

    /**
     * @param array $dimensionsData
     * @return Dimension[]
     */
    private function buildDimensions(array $dimensionsData)
    {
        $dimensions = [];
        foreach ($dimensionsData as $dimensionData) {
            $dimensions[$dimensionData['name']] = $this->dimensionFactory->create($dimensionData);
        }
        return $dimensions;
    }
}
