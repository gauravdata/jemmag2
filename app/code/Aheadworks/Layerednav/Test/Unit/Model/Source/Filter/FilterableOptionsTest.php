<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Source\Filter;

use Aheadworks\Layerednav\Model\Source\Filter\FilterableOptions;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\LayeredNavigation\Model\Attribute\Source\FilterableOptions as FilterableOptionsSource;

/**
 * Test for \Aheadworks\Layerednav\Model\Source\Filter\FilterableOptions
 */
class FilterableOptionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FilterableOptions
     */
    private $model;

    /**
     * @var FilterableOptionsSource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterableOptionsSource;

    /**
     * @var array
     */
    private $options = [
        ['value' => 1, 'label' => 'Option Label']
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->filterableOptionsSource = $this->getMockBuilder(FilterableOptionsSource::class)
            ->setMethods(['toOptionArray'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            FilterableOptions::class,
            [
                'filterableOptionsSource' => $this->filterableOptionsSource
            ]
        );
    }

    /**
     * Test toOptionArray method
     */
    public function testToOptionArray()
    {
        $this->filterableOptionsSource->expects($this->once())
            ->method('toOptionArray')
            ->willReturn($this->options);

        $this->assertEquals($this->options, $this->model->toOptionArray());
    }

    /**
     * Test getOptions method
     */
    public function testGetOptions()
    {
        $this->filterableOptionsSource->expects($this->once())
            ->method('toOptionArray')
            ->willReturn($this->options);

        $result = [
            $this->options[0]['value'] => $this->options[0]['label']
        ];
        $this->assertEquals($result, $this->model->getOptions());
    }

    /**
     * Test getOptionByValue method
     */
    public function testGetOptionByValue()
    {
        $this->filterableOptionsSource->expects($this->once())
            ->method('toOptionArray')
            ->willReturn($this->options);

        $this->assertEquals($this->options[0]['label'], $this->model->getOptionByValue($this->options[0]['value']));
    }
}
