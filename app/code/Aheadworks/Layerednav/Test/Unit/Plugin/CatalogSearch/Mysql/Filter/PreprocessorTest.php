<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Plugin\CatalogSearch\Mysql\Filter;

use Aheadworks\Layerednav\Model\Search\Filter\State as FilterState;
use Aheadworks\Layerednav\Model\Search\Request\FilterChecker;
use Aheadworks\Layerednav\Plugin\CatalogSearch\Mysql\Filter\Preprocessor;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\CatalogSearch\Model\Adapter\Mysql\Filter\Preprocessor as FilterPreprocessor;

/**
 * Test for \Aheadworks\Layerednav\Plugin\CatalogSearch\Mysql\Filter\Preprocessor
 */
class PreprocessorTest extends TestCase
{
    /**
     * @var Preprocessor
     */
    private $plugin;

    /**
     * @var FilterChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterCheckerMock;

    /**
     * @var FilterState|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterStateMock;

    /**
     * @var AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connectionMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->filterCheckerMock = $this->createMock(FilterChecker::class);
        $this->filterStateMock = $this->createMock(FilterState::class);
        $this->connectionMock = $this->createMock(AdapterInterface::class);

        $this->plugin = $objectManager->getObject(
            Preprocessor::class,
            [
                'filterChecker' => $this->filterCheckerMock,
                'filterState' => $this->filterStateMock,
                'connection' => $this->connectionMock
            ]
        );
    }

    /**
     * Test aroundProcess method
     *
     * @param bool $isCustom
     * @param bool $isBaseCategory
     * @param bool $isDoNotUseBaseCategoryFlagSet
     * @param bool $isCategory
     * @param string $expectedResult
     * @dataProvider aroundProcessDataProvider
     */
    public function testAroundProcess(
        $isCustom,
        $isBaseCategory,
        $isDoNotUseBaseCategoryFlagSet,
        $isCategory,
        $expectedResult
    ) {
        $filterPreprocessorMock = $this->createMock(FilterPreprocessor::class);
        $filterMock = $this->createMock(FilterInterface::class);
        $isNegation = false;
        $query = 'default-query with category_ids';
        $processResult = 'default-result';

        $proceed = function ($filter, $isNegation, $query) use ($processResult) {
            return $processResult;
        };

        $this->filterCheckerMock->expects($this->any())
            ->method('isCustom')
            ->willReturn($isCustom);
        $this->filterCheckerMock->expects($this->any())
            ->method('isBaseCategory')
            ->willReturn($isBaseCategory);
        $this->filterCheckerMock->expects($this->any())
            ->method('isCategory')
            ->willReturn($isCategory);

        $this->filterStateMock->expects($this->any())
            ->method('isDoNotUseBaseCategoryFlagSet')
            ->willReturn($isDoNotUseBaseCategoryFlagSet);

        $this->connectionMock->expects($this->any())
            ->method('quoteIdentifier')
            ->will($this->returnCallback([$this, 'quoteIdentifierCallback']));

        $this->assertEquals(
            $expectedResult,
            $this->plugin->aroundProcess($filterPreprocessorMock, $proceed, $filterMock, $isNegation, $query)
        );
    }

    /**
     * @return string
     */
    public function quoteIdentifierCallback()
    {
        $args = func_get_args();
        $param = reset($args);
        return $param;
    }

    /**
     * @return array
     */
    public function aroundProcessDataProvider()
    {
        return [
            [
                'isCustom' => false,
                'isBaseCategory' => false,
                'isDoNotUseBaseCategoryFlagSet' => false,
                'isCategory' => false,
                'expectedResult' => 'default-result'
            ],
            [
                'isCustom' => true,
                'isBaseCategory' => false,
                'isDoNotUseBaseCategoryFlagSet' => false,
                'isCategory' => false,
                'expectedResult' => ''
            ],
            [
                'isCustom' => false,
                'isBaseCategory' => true,
                'isDoNotUseBaseCategoryFlagSet' => false,
                'isCategory' => false,
                'expectedResult' => 'default-result'
            ],
            [
                'isCustom' => false,
                'isBaseCategory' => true,
                'isDoNotUseBaseCategoryFlagSet' => true,
                'isCategory' => false,
                'expectedResult' => ''
            ],
            [
                'isCustom' => false,
                'isBaseCategory' => false,
                'isDoNotUseBaseCategoryFlagSet' => false,
                'isCategory' => true,
                'expectedResult' => 'default-query with category_ids_index.category_id'
            ],
        ];
    }
}
