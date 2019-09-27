<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search\Search;

use Aheadworks\Layerednav\Model\Search\Filter\Checker as FilterChecker;
use Aheadworks\Layerednav\Model\Search\Search\ExtendedAggregationsBuilder;
use Aheadworks\Layerednav\Model\Search\Search\FieldAggregationBuilder;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Exception\StateException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Search\ExtendedAggregationsBuilder
 */
class ExtendedAggregationsBuilderTest extends TestCase
{
    /**
     * @var ExtendedAggregationsBuilder
     */
    private $model;

    /**
     * @var FilterChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterCheckerMock;

    /**
     * @var FieldAggregationBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fieldAggregationBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->filterCheckerMock = $this->createMock(FilterChecker::class);
        $this->fieldAggregationBuilderMock = $this->createMock(FieldAggregationBuilder::class);

        $this->model = $objectManager->getObject(
            ExtendedAggregationsBuilder::class,
            [
                'filterChecker' => $this->filterCheckerMock,
                'fieldAggregationBuilder' => $this->fieldAggregationBuilderMock,
            ]
        );
    }

    /**
     * Test build method
     */
    public function testBuild()
    {
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $scope = '1';

        $extendedAggregationOneMock = $this->createMock(AggregationInterface::class);
        $extendedAggregationTwoMock = $this->createMock(AggregationInterface::class);
        $expectedResult = [$extendedAggregationOneMock, $extendedAggregationTwoMock];

        $fieldsMap = [
            [$searchCriteriaMock, 'filter_one', ['field1'], $scope, $extendedAggregationOneMock],
            [$searchCriteriaMock, 'filter_two', ['field2', 'field3'], $scope, $extendedAggregationTwoMock]
        ];

        $this->filterCheckerMock->expects($this->once())
            ->method('getExtendedFilters')
            ->willReturn(['filter_one' => ['field1'], 'filter_two' => ['field2', 'field3']]);

        $this->fieldAggregationBuilderMock->expects($this->exactly(2))
            ->method('build')
            ->will($this->returnValueMap($fieldsMap));

        $this->assertEquals($expectedResult, $this->model->build($searchCriteriaMock, $scope));
    }

    /**
     * Test build method if no extended filters
     */
    public function testBuildNoExtendedFilters()
    {
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $scope = '1';
        $expectedResult = [];

        $this->filterCheckerMock->expects($this->once())
            ->method('getExtendedFilters')
            ->willReturn([]);

        $this->fieldAggregationBuilderMock->expects($this->never())
            ->method('build');

        $this->assertEquals($expectedResult, $this->model->build($searchCriteriaMock, $scope));
    }

    /**
     * Test build method if na exception occurs
     *
     * @expectedException \Magento\Framework\Exception\StateException
     * @expectedExceptionMessage Error!
     */
    public function testBuildException()
    {
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $scope = '1';

        $this->filterCheckerMock->expects($this->once())
            ->method('getExtendedFilters')
            ->willReturn(['filter_one' => ['field1', 'field2']]);

        $this->fieldAggregationBuilderMock->expects($this->once())
            ->method('build')
            ->with($searchCriteriaMock, 'filter_one', ['field1', 'field2'], $scope)
            ->willThrowException(new StateException(__('Error!')));

        $this->model->build($searchCriteriaMock, $scope);
    }
}
