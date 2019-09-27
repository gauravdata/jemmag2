<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Plugin\Elasticsearch;

use Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\CustomFieldsProvider;
use Aheadworks\Layerednav\Plugin\Elasticsearch\ProductFieldsProvider;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\CompositeFieldProvider;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Psr\Log\LoggerInterface;

/**
 * Test for \Aheadworks\Layerednav\Plugin\Elasticsearch\ProductFieldsProvider
 */
class ProductFieldsProviderTest extends TestCase
{
    /**
     * @var ProductFieldsProvider
     */
    private $plugin;

    /**
     * @var CustomFieldsProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    private $customFieldsProviderMock;

    /**
     * @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $loggerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->customFieldsProviderMock = $this->createMock(CustomFieldsProvider::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->plugin = $objectManager->getObject(
            ProductFieldsProvider::class,
            [
                'customFieldsProvider' => $this->customFieldsProviderMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    /**
     * Test afterGetFields method
     */
    public function testAfterGetFields()
    {
        $subjectMock = $this->createMock(CompositeFieldProvider::class);
        $context = [];
        $result = ['default-data' => []];
        $customFieldsData = ['custom-fields-data' => []];
        $expectedResult = [
            'default-data' => [],
            'custom-fields-data' => []
        ];

        $this->customFieldsProviderMock->expects($this->once())
            ->method('getFields')
            ->with($context)
            ->willReturn($customFieldsData);

        $this->loggerMock->expects($this->never())
            ->method('critical');

        $this->assertEquals($expectedResult, $this->plugin->afterGetFields($subjectMock, $result, $context));
    }

    /**
     * Test afterGetFields method without context array
     */
    public function testAfterGetFieldsWithoutContext()
    {
        $subjectMock = $this->createMock(CompositeFieldProvider::class);
        $result = ['default-data' => []];
        $customFieldsData = ['custom-fields-data' => []];
        $expectedResult = [
            'default-data' => [],
            'custom-fields-data' => []
        ];

        $this->customFieldsProviderMock->expects($this->once())
            ->method('getFields')
            ->with([])
            ->willReturn($customFieldsData);

        $this->loggerMock->expects($this->never())
            ->method('critical');

        $this->assertEquals($expectedResult, $this->plugin->afterGetFields($subjectMock, $result));
    }

    /**
     * Test afterGetFields method if an error occurs
     */
    public function testAfterGetFieldsError()
    {
        $subjectMock = $this->createMock(CompositeFieldProvider::class);
        $context = [];
        $result = [
            'default-data' => []
        ];

        $expectedResult = [
            'default-data' => []
        ];

        $this->customFieldsProviderMock->expects($this->once())
            ->method('getFields')
            ->with($context)
            ->willThrowException(new \Exception('Error!'));

        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with('Error!');

        $this->assertEquals($expectedResult, $this->plugin->afterGetFields($subjectMock, $result, $context));
    }
}
