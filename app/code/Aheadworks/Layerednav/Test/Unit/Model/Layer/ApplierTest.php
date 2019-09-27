<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer;

use Aheadworks\Layerednav\Model\Layer\Applier;
use Aheadworks\Layerednav\Model\Layer\Filter\Applier\Pool as FilterApplierPool;
use Aheadworks\Layerednav\Model\Layer\Filter\Applier\ApplierInterface;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Layer\FilterList;
use Aheadworks\Layerednav\Model\Layer\FilterListResolver;
use Magento\Framework\App\RequestInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Model\Layer;
use Psr\Log\LoggerInterface;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Applier
 */
class ApplierTest extends TestCase
{
    /**
     * @var Applier
     */
    private $model;

    /**
     * @var FilterListResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterListResolverMock;

    /**
     * @var FilterApplierPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterApplierPoolMock;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->filterListResolverMock = $this->createMock(FilterListResolver::class);
        $this->filterApplierPoolMock = $this->createMock(FilterApplierPool::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->model = $objectManager->getObject(
            Applier::class,
            [
                'filterListResolver' => $this->filterListResolverMock,
                'filterApplierPool' => $this->filterApplierPoolMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    /**
     * Test applyFilters method
     *
     * @param RequestInterface|\PHPUnit\Framework\MockObject\MockObject $request
     * @param FilterInterface[]|\PHPUnit\Framework\MockObject\MockObject[] $filters
     * @param array $applierMap
     * @dataProvider applyFiltersDataProvider
     * @throws \ReflectionException
     */
    public function testApplyFilters($request, $filters, $applierMap)
    {
        $this->setProperty('request', $request);

        $layerMock = $this->createMock(Layer::class);
        $layerMock->expects($this->once())
            ->method('apply')
            ->willReturnSelf();

        $filterListMock = $this->createMock(FilterList::class);
        $filterListMock->expects($this->once())
            ->method('getFilters')
            ->with($layerMock)
            ->willReturn($filters);

        $this->filterApplierPoolMock->expects($this->any())
            ->method('getApplier')
            ->willReturnMap($applierMap);

        $this->filterListResolverMock->expects($this->once())
            ->method('get')
            ->willReturn($filterListMock);

        $this->loggerMock->expects($this->never())
            ->method('critical');

        $this->assertNull($this->model->applyFilters($layerMock));
    }

    /**
     * @return array
     */
    public function applyFiltersDataProvider()
    {

        $requestMock = $this->createMock(RequestInterface::class);
        $filterOneMock = $this->getFilterMock('test-one');
        $filterTwoMock = $this->getFilterMock('test-two');

        return [
            [
                'request' => $requestMock,
                'filters' => [],
                'applierMap' => []
            ],
            [
                'request' => $requestMock,
                'filters' => [
                    $filterOneMock
                ],
                'applierMap' => [
                    ['test-one', $this->getApplierMock($requestMock, $filterOneMock)]
                ]
            ],
            [
                'request' => $requestMock,
                'filters' => [
                    $filterOneMock,
                    $filterTwoMock
                ],
                'applierMap' => [
                    ['test-one', $this->getApplierMock($requestMock, $filterOneMock)],
                    ['test-two', $this->getApplierMock($requestMock, $filterTwoMock)]
                ]
            ],
        ];
    }

    /**
     * Test applyFilters method if no applier found
     */
    public function testApplyFiltersNoApplier()
    {
        $requestMock = $this->createMock(RequestInterface::class);
        $this->setProperty('request', $requestMock);

        $layerMock = $this->createMock(Layer::class);
        $layerMock->expects($this->once())
            ->method('apply')
            ->willReturnSelf();

        $filterMock = $this->getFilterMock('test');
        $filters = [$filterMock];

        $filterListMock = $this->createMock(FilterList::class);
        $filterListMock->expects($this->once())
            ->method('getFilters')
            ->with($layerMock)
            ->willReturn($filters);

        $this->filterApplierPoolMock->expects($this->any())
            ->method('getApplier')
            ->with('test')
            ->willThrowException(new \Exception('No applier found!'));

        $this->filterListResolverMock->expects($this->once())
            ->method('get')
            ->willReturn($filterListMock);

        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with('No applier found!');

        $this->assertNull($this->model->applyFilters($layerMock));
    }

    /**
     * Get layer filter mock
     *
     * @param string $type
     * @return FilterInterface|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getFilterMock($type)
    {
        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->any())
            ->method('getType')
            ->willReturn($type);

        return $filterMock;
    }

    /**
     * Get applier mock
     *
     * @param RequestInterface|\PHPUnit_Framework_MockObject_MockObject $requestMock
     * @param FilterInterface|\PHPUnit\Framework\MockObject\MockObject $filterMock
     * @return ApplierInterface|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getApplierMock($requestMock, $filterMock)
    {
        $applierMock = $this->createMock(ApplierInterface::class);
        $applierMock->expects($this->once())
            ->method('apply')
            ->with($requestMock, $filterMock)
            ->willReturn([]);

        return $applierMock;
    }

    /**
     * Set property
     *
     * @param string $propertyName
     * @param mixed $value
     * @return mixed
     * @throws \ReflectionException
     */
    private function setProperty($propertyName, $value)
    {
        $class = new \ReflectionClass($this->model);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->model, $value);

        return $this;
    }
}
