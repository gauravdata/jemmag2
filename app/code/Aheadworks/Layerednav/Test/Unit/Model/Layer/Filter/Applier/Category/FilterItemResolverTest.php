<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Applier\Category;

use Aheadworks\Layerednav\Model\Category\Resolver as CategoryResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\Applier\Category\FilterItemResolver;
use Aheadworks\Layerednav\Model\Seo\Checker as SeoChecker;
use Magento\Framework\Escaper;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Applier\Category\FilterItemResolver
 */
class FilterItemResolverTest extends TestCase
{
    /**
     * @var FilterItemResolver
     */
    private $model;

    /**
     * @var CategoryResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryResolverMock;

    /**
     * @var Escaper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $escaperMock;

    /**
     * @var SeoChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $seoCheckerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->categoryResolverMock = $this->createMock(CategoryResolver::class);
        $this->escaperMock = $this->createMock(Escaper::class);
        $this->seoCheckerMock = $this->createMock(SeoChecker::class);

        $this->model = $objectManager->getObject(
            FilterItemResolver::class,
            [
                'categoryResolver' => $this->categoryResolverMock,
                'escaper' => $this->escaperMock,
                'seoChecker' => $this->seoCheckerMock
            ]
        );
    }

    /**
     * Test getLabel method
     */
    public function testGetLabel()
    {
        $categoryId = 25;
        $categoryName  = 'Category Name';

        $this->categoryResolverMock->expects($this->once())
            ->method('getCategoryName')
            ->with($categoryId)
            ->willReturn($categoryName);

        $this->escaperMock->expects($this->once())
            ->method('escapeHtml')
            ->willReturn($categoryName);

        $this->assertEquals($categoryName, $this->model->getLabel($categoryId));
    }

    /**
     * Test getValue method
     *
     * @param $categoryId
     * @param $categoryUrlKey
     * @param $isNeedToUseTextValues
     * @param $expectedResult
     * @dataProvider getValueDataProvider
     */
    public function testGetValue($categoryId, $categoryUrlKey, $isNeedToUseTextValues, $expectedResult)
    {
        $this->seoCheckerMock->expects($this->once())
            ->method('isNeedToUseTextValues')
            ->willReturn($isNeedToUseTextValues);

        $this->categoryResolverMock->expects($this->any())
            ->method('getCategoryUrlKeys')
            ->with([$categoryId])
            ->willReturn([$categoryUrlKey]);

        $this->assertEquals($expectedResult, $this->model->getValue($categoryId));
    }

    /**
     * @return array
     */
    public function getValueDataProvider()
    {
        return [
            [
                'categoryId' => 25,
                'categoryUrlKey' => 'test-category',
                'isNeedToUseTextValues' => false,
                'expectedResult' => 25
            ],
            [
                'categoryId' => 25,
                'categoryUrlKey' => 'test-category',
                'isNeedToUseTextValues' => true,
                'expectedResult' => 'test-category'
            ]
        ];
    }
}
