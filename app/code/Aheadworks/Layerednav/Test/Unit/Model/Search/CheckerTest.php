<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search;

use Aheadworks\Layerednav\Model\Search\Checker;
use Aheadworks\Layerednav\Model\Search\Filter\Checker as FilterChecker;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Checker
 */
class CheckerTest extends TestCase
{
    /**
     * @var Checker
     */
    private $model;

    /**
     * @var FilterChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterCheckerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->filterCheckerMock = $this->createMock(FilterChecker::class);

        $this->model = $objectManager->getObject(
            Checker::class,
            [
                'filterChecker' => $this->filterCheckerMock,
            ]
        );
    }

    /**
     * Test isExtendedSearchNeeded method
     *
     * @param bool $hasAppliedFilters
     * @param bool $expectedResult
     * @dataProvider isExtendedSearchNeededDataProvider
     */
    public function testIsExtendedSearchNeeded($hasAppliedFilters, $expectedResult)
    {
        $this->filterCheckerMock->expects($this->once())
            ->method('hasAppliedFilters')
            ->willReturn($hasAppliedFilters);

        $this->assertEquals($expectedResult, $this->model->isExtendedSearchNeeded());
    }

    /**
     * @return array
     */
    public function isExtendedSearchNeededDataProvider()
    {
        return [
            [
                'hasAppliedFilters' => true,
                'expectedResult' => true
            ],
            [
                'hasAppliedFilters' => false,
                'expectedResult' => false
            ]
        ];
    }

    /**
     * Test isCategoryFilterApplied method
     *
     * @param bool $isCategoryFilterApplied
     * @param bool $expectedResult
     * @dataProvider isCategoryFilterAppliedDataProvider
     */
    public function testIsCategoryFilterApplied($isCategoryFilterApplied, $expectedResult)
    {
        $this->filterCheckerMock->expects($this->once())
            ->method('isApplied')
            ->with('category_ids_query')
            ->willReturn($isCategoryFilterApplied);

        $this->assertEquals($expectedResult, $this->model->isCategoryFilterApplied());
    }

    /**
     * @return array
     */
    public function isCategoryFilterAppliedDataProvider()
    {
        return [
            [
                'isCategoryFilterApplied' => false,
                'expectedResult' => false
            ],
            [
                'isCategoryFilterApplied' => true,
                'expectedResult' => true
            ],
        ];
    }
}
