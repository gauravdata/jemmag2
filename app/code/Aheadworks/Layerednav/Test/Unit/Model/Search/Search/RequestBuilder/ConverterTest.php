<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search\Search\RequestBuilder;

use Aheadworks\Layerednav\Model\Search\Search\RequestBuilder\Converter;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\Search\Request\Dimension;
use Magento\Framework\Search\Request\DimensionFactory;
use Magento\Framework\Search\Request\Mapper;
use Magento\Framework\Search\Request\MapperFactory;
use Magento\Framework\Search\Request\QueryInterface;
use Magento\Framework\Search\RequestFactory;
use Magento\Framework\Search\RequestInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Search\RequestBuilder\Converter
 */
class ConverterTest extends TestCase
{
    /**
     * @var Converter
     */
    private $model;

    /**
     * @var MapperFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mapperFactoryMock;

    /**
     * @var DimensionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dimensionFactoryMock;

    /**
     * @var RequestFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->mapperFactoryMock = $this->createMock(MapperFactory::class);
        $this->dimensionFactoryMock = $this->createMock(DimensionFactory::class);
        $this->requestFactoryMock = $this->createMock(RequestFactory::class);

        $this->model = $objectManager->getObject(
            Converter::class,
            [
                'mapperFactory' => $this->mapperFactoryMock,
                'dimensionFactory' => $this->dimensionFactoryMock,
                'requestFactory' => $this->requestFactoryMock
            ]
        );
    }

    /**
     * Test convert method
     */
    public function testConvert()
    {
        $data = [
            'dimensions' => [['name' => 'dimension-name', 'data' => 'dimension-data']],
            'queries' => ['queries-data'],
            'filters' => ['filters-data'],
            'aggregations' => ['aggregations-data'],
            'from' => 0,
            'size' => 10000,
            'query' => 'catalog_view_container',
            'index' => 'catalogsearch_fulltext',
            'sort' => [
                [
                    'field' => 'price',
                    'direction' => 'DESC',
                ],
                [
                    'field' => 'entity_id',
                    'direction' => 'DESC',
                ],
            ],
        ];

        $queryMock = $this->createMock(QueryInterface::class);
        $buckets = [$this->createMock(BucketInterface::class)];
        $mapperMock = $this->getMapperMock($queryMock, $buckets);
        $this->mapperFactoryMock->expects($this->once())
            ->method('create')
            ->with([
                'rootQueryName' => $data['query'],
                'queries' => $data['queries'],
                'aggregations' => $data['aggregations'],
                'filters' => $data['filters']
            ])
            ->willReturn($mapperMock);

        $dimensionMock = $this->createMock(Dimension::class);
        $this->dimensionFactoryMock->expects($this->once())
            ->method('create')
            ->with(['name' => 'dimension-name', 'data' => 'dimension-data'])
            ->willReturn($dimensionMock);
        $dimensions = [
            'dimension-name' => $dimensionMock
        ];

        $requestMock = $this->createMock(RequestInterface::class);
        $this->requestFactoryMock->expects($this->once())
            ->method('create')
            ->with([
                'name' => $data['query'],
                'indexName' => $data['index'],
                'from' => $data['from'],
                'size' => $data['size'],
                'query' => $queryMock,
                'dimensions' => $dimensions,
                'buckets' => $buckets,
                'sort' => [
                    [
                        'field' => 'price',
                        'direction' => 'DESC',
                    ],
                    [
                        'field' => 'entity_id',
                        'direction' => 'DESC',
                    ],
                ],
            ])
            ->willReturn($requestMock);

        $this->assertSame($requestMock, $this->model->convert($data));
    }

    /**
     * Get mapper mock
     *
     * @param QueryInterface|\PHPUnit\Framework\MockObject\MockObject $queryMock
     * @param BucketInterface[]||\PHPUnit\Framework\MockObject\MockObject $buckets
     * @return Mapper|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getMapperMock($queryMock, array $buckets)
    {
        $mapperMock = $this->createMock(Mapper::class);
        $mapperMock->expects($this->any())
            ->method('getRootQuery')
            ->willReturn($queryMock);
        $mapperMock->expects($this->any())
            ->method('getBuckets')
            ->willReturn($buckets);

        return $mapperMock;
    }
}
