<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item;

use Aheadworks\Layerednav\Model\Layer\Filter\Interval;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\Resolver;
use Aheadworks\Layerednav\Model\Layer\Filter\Checker as FilterChecker;
use Aheadworks\Layerednav\Model\Layer\Filter\Interval\Resolver as FilterIntervalResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Layer\State\Item as StateItem;
use Aheadworks\Layerednav\Model\Layer\State\Item\Resolver as StateItemResolver;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\Resolver
 */
class ResolverTest extends TestCase
{
    /**
     * @var Resolver
     */
    private $model;

    /**
     * @var StateItemResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stateItemResolverMock;

    /**
     * @var FilterChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterCheckerMock;

    /**
     * @var FilterIntervalResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterIntervalResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->stateItemResolverMock = $this->createMock(StateItemResolver::class);
        $this->filterCheckerMock = $this->createMock(FilterChecker::class);
        $this->filterIntervalResolverMock = $this->createMock(FilterIntervalResolver::class);

        $this->model = $objectManager->getObject(
            Resolver::class,
            [
                'stateItemResolver' => $this->stateItemResolverMock,
                'filterChecker' => $this->filterCheckerMock,
                'filterIntervalResolver' => $this->filterIntervalResolverMock,
            ]
        );
    }

    /**
     * Test getActiveItemByFilter method
     *
     * @param $stateItem
     * @param $expectedResult
     * @dataProvider getActiveItemByFilterDataProvider
     * @throws \ReflectionException
     */
    public function testGetActiveItemByFilter($stateItem, $expectedResult)
    {
        $filterMock = $this->createMock(FilterInterface::class);

        $this->stateItemResolverMock->expects($this->once())
            ->method('getItemByFilter')
            ->with($filterMock)
            ->willReturn($stateItem);

        $this->assertSame($expectedResult, $this->model->getActiveItemByFilter($filterMock));
    }

    /**
     * @return array
     */
    public function getActiveItemByFilterDataProvider()
    {
        $filterItemMock = $this->createMock(FilterItemInterface::class);

        $stateItemMock = $this->createMock(StateItem::class);
        $stateItemMock->expects($this->once())
            ->method('getFilterItem')
            ->willReturn($filterItemMock);

        return [
            [
                'stateItem' => null,
                'expectedResult' => null
            ],
            [
                'stateItem' => $stateItemMock,
                'expectedResult' => $filterItemMock
            ]
        ];
    }

    /**
     * Test getPriceFromValue method
     *
     * @param bool $isPrice
     * @param string $value
     * @param Interval|\PHPUnit_Framework_MockObject_MockObject|false $interval
     * @param float $expectedResult
     * @dataProvider getPriceFromValueDataProvider
     * @throws \ReflectionException
     */
    public function testGetPriceFromValue($isPrice, $value, $interval, $expectedResult)
    {
        $filterMock = $this->createMock(FilterInterface::class);

        $filterItemMock = $this->createMock(FilterItemInterface::class);
        $filterItemMock->expects($this->any())
            ->method('getFilter')
            ->willReturn($filterMock);
        $filterItemMock->expects($this->any())
            ->method('getValue')
            ->willReturn($value);

        $this->filterCheckerMock->expects($this->once())
            ->method('isPrice')
            ->with($filterMock)
            ->willReturn($isPrice);

        $this->filterIntervalResolverMock->expects($this->any())
            ->method('getInterval')
            ->with($value)
            ->willReturn($interval);

        $this->assertEquals($expectedResult, $this->model->getPriceFromValue($filterItemMock));
    }

    /**
     * @return array
     */
    public function getPriceFromValueDataProvider()
    {
        return [
            [
                'isPrice' => false,
                'value' => '125',
                'interval' => false,
                'expectedResult' => 0
            ],
            [
                'isPrice' => true,
                'value' => 'AA232',
                'interval' => false,
                'expectedResult' => 0
            ],
            [
                'isPrice' => true,
                'value' => '3.45-25.45',
                'interval' => $this->getIntervalMock(3.45, 25.45),
                'expectedResult' => 3.45
            ],
        ];
    }

    /**
     * Test getPriceToValue method
     *
     * @param bool $isPrice
     * @param string $value
     * @param Interval|\PHPUnit_Framework_MockObject_MockObject|false $interval
     * @param float $expectedResult
     * @dataProvider getPriceToValueDataProvider
     * @throws \ReflectionException
     */
    public function testGetPriceToValue($isPrice, $value, $interval, $expectedResult)
    {
        $filterMock = $this->createMock(FilterInterface::class);

        $filterItemMock = $this->createMock(FilterItemInterface::class);
        $filterItemMock->expects($this->any())
            ->method('getFilter')
            ->willReturn($filterMock);
        $filterItemMock->expects($this->any())
            ->method('getValue')
            ->willReturn($value);

        $this->filterCheckerMock->expects($this->once())
            ->method('isPrice')
            ->with($filterMock)
            ->willReturn($isPrice);

        $this->filterIntervalResolverMock->expects($this->any())
            ->method('getInterval')
            ->with($value)
            ->willReturn($interval);

        $this->assertEquals($expectedResult, $this->model->getPriceToValue($filterItemMock));
    }

    /**
     * @return array
     */
    public function getPriceToValueDataProvider()
    {
        return [
            [
                'isPrice' => false,
                'value' => '125',
                'interval' => false,
                'expectedResult' => 0
            ],
            [
                'isPrice' => true,
                'value' => 'AA232',
                'interval' => false,
                'expectedResult' => 0
            ],
            [
                'isPrice' => true,
                'value' => '3.45-25.45',
                'interval' => $this->getIntervalMock(3.45, 25.45),
                'expectedResult' => 25.45
            ]
        ];
    }

    /**
     * Get interval mock
     *
     * @param float $from
     * @param float $to
     * @return Interval|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getIntervalMock($from, $to)
    {
        $intervalMock = $this->createMock(Interval::class);
        $intervalMock->expects($this->any())
            ->method('getFrom')
            ->willReturn($from);
        $intervalMock->expects($this->any())
            ->method('getTo')
            ->willReturn($to);

        return $intervalMock;
    }
}
