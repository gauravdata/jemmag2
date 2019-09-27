<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item\DataProvider\Custom;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Custom\OptionsPreparer;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer\Option as OptionPreparer;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Seo\Checker as SeoChecker;
use Magento\Framework\Filter\FilterManager;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Custom\OptionsPreparer
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
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->optionPreparerMock = $this->createMock(OptionPreparer::class);
        $this->filterManagerMock = $this->createPartialMock(FilterManager::class, ['stripTags']);
        $this->seoCheckerMock = $this->createMock(SeoChecker::class);

        $this->model = $objectManager->getObject(
            OptionsPreparer::class,
            [
                'optionPreparer' => $this->optionPreparerMock,
                'filterManager' => $this->filterManagerMock,
                'seoChecker' => $this->seoCheckerMock,
            ]
        );
    }

    /**
     * Test perform method
     *
     * @param bool $isNeedToUseTextValues
     * @param string $filterType
     * @param array $preparedOptions
     * @param array $expectedResult
     * @dataProvider performDataProvider
     * @throws \ReflectionException
     */
    public function testPerform($isNeedToUseTextValues, $filterType, $preparedOptions, $expectedResult)
    {
        $options = ['options_data'];
        $optionsCounts = ['options_counts_data'];

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->any())
            ->method('getType')
            ->willReturn($filterType);

        $this->optionPreparerMock->expects($this->once())
            ->method('perform')
            ->with($options, $optionsCounts, true)
            ->willReturn($preparedOptions);

        $this->seoCheckerMock->expects($this->once())
            ->method('isNeedToUseTextValues')
            ->willReturn($isNeedToUseTextValues);

        $this->filterManagerMock->expects($this->once())
            ->method('stripTags')
            ->willReturnMap([
                ['Test', 'Test without tags']
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
                'filterType' => 'in-stock',
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
                'filterType' => 'in-stock',
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
                        'value' => 'in-stock',
                        'count' => 14,
                        'image' => []
                    ]
                ]
            ]
        ];
    }
}
