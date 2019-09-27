<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search\Request\Container;

use Aheadworks\Layerednav\Model\Search\Request\Container\Cleaner;
use Aheadworks\Layerednav\Model\Search\Request\Container\Cleaner\CleanerInterface;
use Magento\Framework\DataObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Request\Container\Cleaner
 */
class CleanerTest extends TestCase
{
    /**
     * @var Cleaner
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

        $this->model = $objectManager->getObject(Cleaner::class, []);
    }

    /**
     * Test perform method
     *
     * @param array $data
     * @param CleanerInterface[]|\PHPUnit\Framework\MockObject\MockObject[] $cleaners
     * @param array $expectedResult
     * @dataProvider performDataProvider
     * @throws \ReflectionException
     */
    public function testPerform($data, $cleaners, $expectedResult)
    {
        $this->setProperty('cleaners', $cleaners);

        $this->assertEquals($expectedResult, $this->model->perform($data));
    }

    /**
     * @return array
     */
    public function performDataProvider()
    {
        return [
            [
                'data' => ['data'],
                'cleaners' => [],
                'expectedResult' => ['data']
            ],
            [
                'data' => ['data'],
                'cleaners' => [
                    $this->getCleanerMock(['data'], ['cleaned-data'])
                ],
                'expectedResult' => ['cleaned-data']
            ],
            [
                'data' => ['data'],
                'cleaners' => [
                    $this->getCleanerMock(['data'], ['cleaned-data1']),
                    $this->getCleanerMock(['cleaned-data1'], ['cleaned-data2'])
                ],
                'expectedResult' => ['cleaned-data2']
            ],
        ];
    }

    /**
     * Test perform method if bad cleaner specified
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Cleaner must implement
     * Aheadworks\Layerednav\Model\Search\Request\Container\Cleaner\CleanerInterface
     */
    public function testPerformBadCleaner()
    {
        $badCleanerMock = $this->createMock(DataObject::class);
        $this->setProperty('cleaners', [$badCleanerMock]);

        $this->model->perform(['data']);
    }

    /**
     * Get cleaner mock
     *
     * @param array $dataToClean
     * @param array $cleanedData
     * @return CleanerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getCleanerMock(array $dataToClean, array $cleanedData)
    {
        $cleanerMock = $this->createMock(CleanerInterface::class);
        $cleanerMock->expects($this->once())
            ->method('perform')
            ->with($dataToClean)
            ->willReturn($cleanedData);

        return $cleanerMock;
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
