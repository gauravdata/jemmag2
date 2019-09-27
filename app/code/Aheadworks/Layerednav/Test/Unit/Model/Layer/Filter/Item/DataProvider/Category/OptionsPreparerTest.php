<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item\DataProvider\Category;

use Aheadworks\Layerednav\Model\Category\Resolver as CategoryResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Category\OptionsPreparer;
use Aheadworks\Layerednav\Model\Seo\Checker as SeoChecker;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Escaper;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Category\OptionsPreparer
 */
class OptionsPreparerTest extends TestCase
{
    /**
     * @var OptionsPreparer
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
            OptionsPreparer::class,
            [
                'categoryResolver' => $this->categoryResolverMock,
                'escaper' => $this->escaperMock,
                'seoChecker' => $this->seoCheckerMock,
            ]
        );
    }

    /**
     * Test perform method
     *
     * @param bool $isNeedToUseTextValues
     * @param Category[]|\PHPUnit_Framework_MockObject_MockObject[] $categories
     * @param array $categoriesMap
     * @param array $categoriesCounts
     * @param array $expectedResult
     * @dataProvider performDataProvider
     * @throws \ReflectionException
     */
    public function testPerform($isNeedToUseTextValues, $categories, $categoriesMap, $categoriesCounts, $expectedResult)
    {
        $categoryMock = $this->createMock(Category::class);
        $categoryMock->expects($this->any())
            ->method('getChildrenCategories')
            ->willReturn($categories);

        $this->categoryResolverMock->expects($this->any())
            ->method('getCategoryUrlKeys')
            ->willReturnMap($categoriesMap);

        $this->seoCheckerMock->expects($this->any())
            ->method('isNeedToUseTextValues')
            ->willReturn($isNeedToUseTextValues);

        $this->escaperMock->expects($this->any())
            ->method('escapeHtml')
            ->willReturnMap([
                ['Category 1', null, 'Category 1 Escaped'],
                ['Category 2', null, 'Category 2 Escaped'],
                ['Category 3', null, 'Category 3 Escaped'],
            ]);

        $this->assertEquals($expectedResult, $this->model->perform($categoryMock, $categoriesCounts));
    }

    /**
     * @return array
     */
    public function performDataProvider()
    {
        return [
            [
                'isNeedToUseTextValues' => false,
                'categories' => [],
                'categoriesMap' => [],
                'categoriesCounts' => [],
                'expectedResult' => []
            ],
            [
                'isNeedToUseTextValues' => false,
                'categories' => [
                    $this->getCategoryMock(125, 'Category 1', true),
                    $this->getCategoryMock(126, 'Category 2', false),
                    $this->getCategoryMock(127, 'Category 3', true),
                ],
                'categoriesMap' => [
                    [['125'], ['category-one']],
                    [['126'], ['category-two']],
                    [['127'], ['category-three']]
                ],
                'categoriesCounts' => [
                    125 => [
                        'value' => 125,
                        'count' => 5
                    ],
                    126 => [
                        'value' => 126,
                        'count' => 11
                    ],
                    127 => [
                        'value' => 125,
                        'count' => 7
                    ],
                ],
                'expectedResult' => [
                    [
                        'label' => 'Category 1 Escaped',
                        'value' => 125,
                        'count' =>  5
                    ],
                    [
                        'label' => 'Category 3 Escaped',
                        'value' => 127,
                        'count' =>  7
                    ],
                ]
            ],
            [
                'isNeedToUseTextValues' => true,
                'categories' => [
                    $this->getCategoryMock(125, 'Category 1', true),
                    $this->getCategoryMock(126, 'Category 2', false),
                    $this->getCategoryMock(127, 'Category 3', true),
                ],
                'categoriesMap' => [
                    [['125'], ['category-one']],
                    [['126'], ['category-two']],
                    [['127'], ['category-three']]
                ],
                'categoriesCounts' => [
                    125 => [
                        'value' => 125,
                        'count' => 5
                    ],
                    126 => [
                        'value' => 126,
                        'count' => 11
                    ],
                    127 => [
                        'value' => 125,
                        'count' => 7
                    ],
                ],
                'expectedResult' => [
                    [
                        'label' => 'Category 1 Escaped',
                        'value' => 'category-one',
                        'count' =>  5
                    ],
                    [
                        'label' => 'Category 3 Escaped',
                        'value' => 'category-three',
                        'count' =>  7
                    ],
                ]
            ],
        ];
    }

    /**
     * Get category mock
     *
     * @param int $id
     * @param string $name
     * @param bool $isActive
     * @return CategoryInterface|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getCategoryMock($id, $name, $isActive)
    {
        $categoryMock = $this->createMock(CategoryInterface::class);
        $categoryMock->expects($this->any())
            ->method('getId')
            ->willReturn($id);
        $categoryMock->expects($this->any())
            ->method('getName')
            ->willReturn($name);
        $categoryMock->expects($this->any())
            ->method('getIsActive')
            ->willReturn($isActive);

        return $categoryMock;
    }
}
