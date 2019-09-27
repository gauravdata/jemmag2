<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Applier\Attribute;

use Aheadworks\Layerednav\Model\Layer\Filter\Applier\Attribute\ValueResolver;
use Aheadworks\Layerednav\Model\Seo\Checker as SeoChecker;
use Magento\Framework\Filter\FilterManager;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\DateResolver
 */
class ValueResolverTest extends TestCase
{
    /**
     * @var ValueResolver
     */
    private $model;

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

        $this->filterManagerMock = $this->createpartialMock(FilterManager::class, ['translitUrl']);
        $this->seoCheckerMock = $this->createMock(SeoChecker::class);

        $this->model = $objectManager->getObject(
            ValueResolver::class,
            [
                'filterManager' => $this->filterManagerMock,
                'seoChecker' => $this->seoCheckerMock
            ]
        );
    }

    /**
     * Test getValue method
     *
     * @param $label
     * @param $value
     * @param $isNeedToUseTextValues
     * @param $expectedResult
     * @dataProvider getValueDataProvider
     */
    public function testGetValue($label, $value, $isNeedToUseTextValues, $expectedResult)
    {
        $this->seoCheckerMock->expects($this->once())
            ->method('isNeedToUseTextValues')
            ->willReturn($isNeedToUseTextValues);

        if ($isNeedToUseTextValues) {
            $this->filterManagerMock->expects($this->once())
                ->method('translitUrl')
                ->with($label)
                ->willReturn($label);
        } else {
            $this->filterManagerMock->expects($this->never())
                ->method('translitUrl');
        }
        $this->assertEquals($expectedResult, $this->model->getValue($label, $value));
    }

    /**
     * @return array
     */
    public function getValueDataProvider()
    {
        return [
            [
                'label' => 'label',
                'value' => '125',
                'isNeedToUseTextValues' => false,
                'expectedResult' => '125'
            ],
            [
                'label' => 'label',
                'value' => '125',
                'isNeedToUseTextValues' => true,
                'expectedResult' => 'label'
            ],
            [
                'label' => 'label',
                'value' => 125,
                'isNeedToUseTextValues' => false,
                'expectedResult' => 125
            ],
            [
                'label' => 'label',
                'value' => 125,
                'isNeedToUseTextValues' => true,
                'expectedResult' => 'label'
            ],
        ];
    }
}
