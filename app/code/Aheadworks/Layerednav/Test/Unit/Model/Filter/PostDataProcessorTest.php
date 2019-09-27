<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter;

use Aheadworks\Layerednav\Model\Filter\PostDataProcessor;
use Aheadworks\Layerednav\Model\Filter\PostDataProcessorInterface;
use Magento\Framework\DataObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\PostDataProcessor
 */
class PostDataProcessorTest extends TestCase
{
    /**
     * @var PostDataProcessor
     */
    private $model;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->model = $objectManager->getObject(
            PostDataProcessor::class,
            []
        );
    }

    /**
     * Test process method
     */
    public function testProcess()
    {
        $data = ['initial-data'];
        $partiallyProcessedData =  ['processed-data-one'];
        $processedData = ['processed-data-two'];

        $processorOneMock = $this->getProcessorMock($data, $partiallyProcessedData);
        $processorTwoMock = $this->getProcessorMock($partiallyProcessedData, $processedData);

        $processors = [$processorOneMock, $processorTwoMock];

        $this->setProperty('processors', $processors);

        $this->assertEquals($processedData, $this->model->process($data));
    }

    /**
     * Test process method if no processors specified
     */
    public function testProcessNoProcessors()
    {
        $data = ['initial-data'];

        $this->setProperty('processors', []);

        $this->assertEquals($data, $this->model->process($data));
    }

    /**
     * Test process method if bad processor specified
     *
     * @expectedException \Exception
     * @expectedExceptionMessage does not implement PostDataProcessorInterface.
     */
    public function testProcessBadProcessor()
    {
        $data = ['initial-data'];
        $partiallyProcessedData =  ['processed-data-one'];

        $processorGoodMock = $this->getProcessorMock($data, $partiallyProcessedData);
        $processorBadMock = $this->createMock(DataObject::class);

        $processors = [$processorGoodMock, $processorBadMock];

        $this->setProperty('processors', $processors);

        $this->model->process($data);
    }

    /**
     * Get processor mock
     *
     * @param array $data
     * @param array $processedData
     * @return PostDataProcessorInterface|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getProcessorMock(array $data, array $processedData)
    {
        $processorMock = $this->createMock(PostDataProcessorInterface::class);
        $processorMock->expects($this->once())
            ->method('process')
            ->with($data)
            ->willReturn($processedData);

        return $processorMock;
    }

    /**
     * Set property
     *
     * @param string $propertyName
     * @param mixed $value
     * @return mixed
     * @throws \ReflectionException
     */
    private function setProperty($propertyName, $value)
    {
        $class = new \ReflectionClass($this->model);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->model, $value);

        return $this;
    }
}
