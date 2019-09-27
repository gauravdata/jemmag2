<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search\Search;

use Aheadworks\Layerednav\Model\Search\Search\RequestBuilder;
use Aheadworks\Layerednav\Model\Search\Search\RequestBuilder\Converter;
use Magento\Framework\Search\Request\Binder;
use Magento\Framework\Search\Request\Cleaner;
use Magento\Framework\Search\Request\Config;
use Magento\Framework\Search\RequestInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Search\RequestBuilder
 */
class RequestBuilderTest extends TestCase
{
    /**
     * @var RequestBuilder
     */
    private $model;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var Binder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $binderMock;

    /**
     * @var Cleaner|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cleanerMock;

    /**
     * @var Converter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $converterMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createMock(Config::class);
        $this->binderMock = $this->createMock(Binder::class);
        $this->cleanerMock = $this->createMock(Cleaner::class);
        $this->converterMock = $this->createMock(Converter::class);

        $this->model = $objectManager->getObject(
            RequestBuilder::class,
            [
                'config' => $this->configMock,
                'binder' => $this->binderMock,
                'cleaner' => $this->cleanerMock,
                'converter' => $this->converterMock,
            ]
        );
    }

    /**
     * Test setRequestName method
     */
    public function testSetRequestName()
    {
        $requestName = 'test-request';
        $this->assertSame($this->model, $this->model->setRequestName($requestName));
        $data =  $this->getProperty('data');

        $this->assertTrue(isset($data['requestName']));
        $this->assertEquals($requestName, $data['requestName']);
    }

    /**
     * Test setSize method
     */
    public function testSetSize()
    {
        $size = 100;
        $this->assertSame($this->model, $this->model->setSize($size));
        $data =  $this->getProperty('data');

        $this->assertTrue(isset($data['size']));
        $this->assertEquals($size, $data['size']);
    }

    /**
     * Test setFrom method
     */
    public function testSetFrom()
    {
        $from = 10;
        $this->assertSame($this->model, $this->model->setFrom($from));
        $data =  $this->getProperty('data');

        $this->assertTrue(isset($data['from']));
        $this->assertEquals($from, $data['from']);
    }

    /**
     * Test setSort method
     */
    public function testSetSort()
    {
        $sortOrders = [
            'price' => 'DESC',
            'entity_id' => 'DESC',
        ];
        $this->assertSame($this->model, $this->model->setSort($sortOrders));
        $data =  $this->getProperty('data');

        $this->assertTrue(isset($data['sort']));
        $this->assertEquals($sortOrders, $data['sort']);
    }

    /**
     * Test bindDimension method
     */
    public function testBindDimension()
    {
        $name = 'name';
        $value = 'value';
        $this->assertSame($this->model, $this->model->bindDimension($name, $value));
        $data =  $this->getProperty('data');

        $this->assertTrue(isset($data['dimensions']) && isset($data['dimensions'][$name]));
        $this->assertEquals($value, $data['dimensions'][$name]);
    }

    /**
     * Test setAllowedAggregations method
     */
    public function testSetAllowedAggregations()
    {
        $list = ['aggregation1', 'aggregation99'];
        $this->model->setAllowedAggregations($list);
        $allowedAggregations =  $this->getProperty('allowedAggregations');

        $this->assertTrue(is_array($allowedAggregations) && !empty($allowedAggregations));
        $this->assertEquals($allowedAggregations, $list);
    }

    /**
     * Test bind method
     */
    public function testBind()
    {
        $placeholder = 'placeholder';
        $value = 'value';
        $this->assertSame($this->model, $this->model->bind($placeholder, $value));
        $data =  $this->getProperty('data');

        $this->assertTrue(isset($data['placeholder']));
        $this->assertEquals(['$placeholder$' => 'value'], $data['placeholder']);
    }

    /**
     * Test create method
     *
     * @param $aggregations
     * @param $allowedAggregations
     * @param $expectedAggregations
     * @dataProvider createDataProvider
     * @throws \Magento\Framework\Exception\StateException
     * @throws \ReflectionException
     */
    public function testCreate($aggregations, $allowedAggregations, $expectedAggregations)
    {
        $requestName = 'request_name';
        $data = [
            'requestName' => $requestName,
            'sort' => [
                'price' => 'DESC',
                'entity_id' => 'DESC',
            ],
        ];
        $this->setProperty('data', $data);
        $this->setProperty('allowedAggregations', $allowedAggregations);

        $configData = [
            'requestName' => $requestName,
            'aggregations' => $aggregations,
            'sort' => [
                [
                    'field' => 'price',
                    'direction' => 'DESC',
                ],
                [
                    'field' => 'entity_id',
                    'direction' => 'DESC',
                ],
            ],
        ];
        $dataToBind = [
            'requestName' => $requestName,
            'aggregations' => $expectedAggregations,
            'sort' => [
                [
                    'field' => 'price',
                    'direction' => 'DESC',
                ],
                [
                    'field' => 'entity_id',
                    'direction' => 'DESC',
                ],
            ],
        ];

        $this->configMock->expects($this->once())
            ->method('get')
            ->with($requestName)
            ->willReturn($configData);

        $this->binderMock->expects($this->once())
            ->method('bind')
            ->with($dataToBind, $data)
            ->willReturn($configData);
        $this->cleanerMock->expects($this->once())
            ->method('clean')
            ->with($configData)
            ->willReturn($configData);

        $requestMock = $this->createMock(RequestInterface::class);
        $this->converterMock->expects($this->once())
            ->method('convert')
            ->with($configData)
            ->willReturn($requestMock);

        $this->assertSame($requestMock, $this->model->create());
        $this->assertEquals(
            [
                'dimensions' => [],
                'placeholder' => []
            ],
            $this->getProperty('data')
        );
        $this->assertEquals([], $this->getProperty('allowedAggregations'));
    }

    /**
     * @return array
     */
    public function createDataProvider()
    {
        return [
            [
                'aggregations' => [
                    'bucket1' => 'data1',
                    'bucket2' => 'data2',
                    'bucket3' => 'data3',
                    'bucket4' => 'data4'
                ],
                'allowedAggregations' => [
                    'bucket2',
                    'bucket3'
                ],
                'expectedAggregations' => [
                    'bucket2' => 'data2',
                    'bucket3' => 'data3',
                ],
            ],
            [
                'aggregations' => [
                    'bucket1' => 'data1',
                    'bucket2' => 'data2',
                    'bucket3' => 'data3',
                    'bucket4' => 'data4'
                ],
                'allowedAggregations' => [],
                'expectedAggregations' => [
                    'bucket1' => 'data1',
                    'bucket2' => 'data2',
                    'bucket3' => 'data3',
                    'bucket4' => 'data4'
                ],
            ],
            [
                'aggregations' => [],
                'allowedAggregations' => [
                    'bucket2',
                    'bucket3'
                ],
                'expectedAggregations' => [],
            ],
        ];
    }

    /**
     * Test create method if request name is not defined
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Request name not defined.
     */
    public function testCreateRequestNameNotDefined()
    {
        $this->model->create();
    }

    /**
     * Test create method if request does not exist
     *
     * @expectedException \Magento\Framework\Search\Request\NonExistingRequestNameException
     * @expectedExceptionMessage Request name 'request_name' doesn't exist.
     */
    public function testCreateRequestNotExist()
    {
        $requestName = 'request_name';
        $data = [
            'requestName' => $requestName
        ];
        $this->setProperty('data', $data);

        $this->configMock->expects($this->once())
            ->method('get')
            ->with($requestName)
            ->willReturn(null);

        $this->model->create();
    }

    /**
     * Test addFieldToFilter method
     *
     * @param string $field
     * @param array $condition
     * @param array $expectedResult
     * @dataProvider addFieldToFilterDataProvider
     * @throws \ReflectionException
     */
    public function testAddFieldToFilter($field, $condition, $expectedResult)
    {
        $this->assertSame($this->model, $this->model->addFieldToFilter($field, $condition));
        $data =  $this->getProperty('data');

        $this->assertTrue(isset($data['placeholder']));
        $this->assertEquals($expectedResult, $data['placeholder']);
    }

    /**
     * @return array
     */
    public function addFieldToFilterDataProvider()
    {
        return [
            [
                'field' => 'test',
                'condition' => 'data',
                'expectedResult' => ['$test$' => 'data']
            ],
            [
                'field' => 'test',
                'condition' => ['from' => 10, 'to' => 20],
                'expectedResult' => ['$test.from$' => 10, '$test.to$' => 20]
            ],
            [
                'field' => 'test',
                'condition' => ['from' => 10],
                'expectedResult' => ['$test.from$' => 10]
            ],
            [
                'field' => 'test',
                'condition' => ['to' => 20],
                'expectedResult' => ['$test.to$' => 20]
            ],
        ];
    }

    /**
     * Get property
     *
     * @param string $propertyName
     * @return mixed
     * @throws \ReflectionException
     */
    private function getProperty($propertyName)
    {
        $class = new \ReflectionClass($this->model);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($this->model);
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
