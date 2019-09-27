<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item\DataProvider\Attribute;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Attribute\OptionsPreparer;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer\Option as OptionPreparer;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer\Swatch as SwatchPreparer;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Product\Attribute\Checker as ProductAttributeChecker;
use Aheadworks\Layerednav\Model\Seo\Checker as SeoChecker;
use Magento\Framework\Filter\FilterManager;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Attribute\OptionsPreparer
 */
class OptionsPreparerTest extends TestCase
{
    /**
     * @var OptionsPreparer
     */
    private $model;

    /**
     * @var OptionPreparer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $optionPreparerMock;

    /**
     * @var FilterManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterManagerMock;

    /**
     * @var SeoChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $seoCheckerMock;

    /**
     * @var SwatchPreparer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $swatchPreparerMock;

    /**
     * @var ProductAttributeChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productAttributeCheckerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->optionPreparerMock = $this->createMock(OptionPreparer::class);
        $this->filterManagerMock = $this->createPartialMock(
            FilterManager::class,
            ['stripTags', 'translitUrl']
        );
        $this->seoCheckerMock = $this->createMock(SeoChecker::class);
        $this->swatchPreparerMock = $this->createMock(SwatchPreparer::class);
        $this->productAttributeCheckerMock = $this->createMock(ProductAttributeChecker::class);

        $this->model = $objectManager->getObject(
            OptionsPreparer::class,
            [
                'optionPreparer' => $this->optionPreparerMock,
                'filterManager' => $this->filterManagerMock,
                'seoChecker' => $this->seoCheckerMock,
                'swatchPreparer' => $this->swatchPreparerMock,
                'productAttributeChecker' => $this->productAttributeCheckerMock
            ]
        );
    }

    /**
     * Test perform method
     *
     * @param bool $isNeedToUseTextValues
     * @param bool $areExtraSwatchesAllowed
     * @param array $preparedOptions
     * @param array $expectedResult
     * @dataProvider performDataProvider
     * @throws \ReflectionException
     */
    public function testPerform($isNeedToUseTextValues, $areExtraSwatchesAllowed, $preparedOptions, $expectedResult)
    {
        $options = ['options_data'];
        $optionsCounts = ['options_counts_data'];

        $attributeMock = $this->createMock(EavAttribute::class);
        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->any())
            ->method('getAttributeModel')
            ->willReturn($attributeMock);

        $this->optionPreparerMock->expects($this->once())
            ->method('perform')
            ->with($options, $optionsCounts, true)
            ->willReturn($preparedOptions);

        $this->productAttributeCheckerMock->expects($this->once())
            ->method('areExtraSwatchesAllowed')
            ->with($attributeMock)
            ->willReturn($areExtraSwatchesAllowed);
        if ($areExtraSwatchesAllowed) {
            $this->swatchPreparerMock->expects($this->once())
                ->method('perform')
                ->with($preparedOptions)
                ->willReturn($preparedOptions);
        }

        $this->seoCheckerMock->expects($this->once())
            ->method('isNeedToUseTextValues')
            ->willReturn($isNeedToUseTextValues);

        $this->filterManagerMock->expects($this->any())
            ->method('stripTags')
            ->willReturnMap([
                ['Test', 'Test without tags']
            ]);
        $this->filterManagerMock->expects($this->any())
            ->method('translitUrl')
            ->willReturnMap([
                ['Test', 'encoded-test'],
                ['Test+without+tags', 'encoded-test-without-tags']
            ]);

        $this->assertEquals($expectedResult, $this->model->perform($filterMock, $options, $optionsCounts, true));
    }

    /**
     * @return array
     */
    public function performDataProvider()
    {
        return [
            [
                'isNeedToUseTextValues' => false,
                'areExtraSwatchesAllowed' => false,
                'preparedOptions' => [
                    [
                        'label' => 'Test',
                        'value' => '1',
                        'count' => 4
                    ]
                ],
                'expectedResult' => [
                    [
                        'label' => 'Test without tags',
                        'value' => '1',
                        'count' => 4,
                        'image' => []
                    ]
                ]
            ],
            [
                'isNeedToUseTextValues' => true,
                'areExtraSwatchesAllowed' => false,
                'preparedOptions' => [
                    [
                        'label' => 'Test',
                        'value' => '1',
                        'count' => 14
                    ]
                ],
                'expectedResult' => [
                    [
                        'label' => 'Test without tags',
                        'value' => 'encoded-test-without-tags',
                        'count' => 14,
                        'image' => []
                    ]
                ]
            ],
            [
                'isNeedToUseTextValues' => false,
                'areExtraSwatchesAllowed' => true,
                'preparedOptions' => [
                    [
                        'label' => 'Test',
                        'value' => '1',
                        'count' => 4
                    ]
                ],
                'expectedResult' => [
                    [
                        'label' => 'Test without tags',
                        'value' => '1',
                        'count' => 4,
                        'image' => []
                    ]
                ]
            ],
            [
                'isNeedToUseTextValues' => true,
                'areExtraSwatchesAllowed' => true,
                'preparedOptions' => [
                    [
                        'label' => 'Test',
                        'value' => '1',
                        'count' => 14
                    ]
                ],
                'expectedResult' => [
                    [
                        'label' => 'Test without tags',
                        'value' => 'encoded-test-without-tags',
                        'count' => 14,
                        'image' => []
                    ]
                ]
            ]
        ];
    }
}
