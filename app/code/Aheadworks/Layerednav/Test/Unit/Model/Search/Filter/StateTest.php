<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search\Filter;

use Aheadworks\Layerednav\Model\Search\Filter\State;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Filter\State
 */
class StateTest extends TestCase
{
    /**
     * @var State
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

        $this->model = $objectManager->getObject(State::class, []);
    }

    /**
     * Test isSetFlag method
     *
     * @param array $flags
     * @param string $flag
     * @param bool $expectedResult
     * @dataProvider isSetFlagDataProvider
     * @throws \ReflectionException
     */
    public function testIsSetFlag($flags, $flag, $expectedResult)
    {
        $this->setProperty('flags', $flags);

        $this->assertEquals($expectedResult, $this->model->isSetFlag($flag));
    }

    /**
     * @return array
     */
    public function isSetFlagDataProvider()
    {
        return [
            [
                'flags' => [],
                'flag' => 'test',
                'expectedResult' => false
            ],
            [
                'flags' => ['test' => true],
                'flag' => 'test',
                'expectedResult' => true
            ],
            [
                'flags' => ['test1' => true, 'test2' => true],
                'flag' => 'test',
                'expectedResult' => false
            ]
        ];
    }

    /**
     * Test setFlag method
     *
     * @param $flags
     * @param $flag
     * @param $expectedResult
     * @dataProvider setFlagDataProvider
     * @throws \ReflectionException
     */
    public function testSetFlag($flags, $flag, $expectedResult)
    {
        $this->setProperty('flags', $flags);

        $this->assertEquals($this->model, $this->model->setFlag($flag));
        $this->assertEquals($expectedResult, $this->getProperty('flags'));
    }

    /**
     * @return array
     */
    public function setFlagDataProvider()
    {
        return [
            [
                'flags' => [],
                'flag' => 'test',
                'expectedResult' => [
                    'test' => true
                ]
            ],
            [
                'flags' => ['test' => true],
                'flag' => 'test2',
                'expectedResult' => [
                    'test' => true,
                    'test2' => true
                ]
            ],
            [
                'flags' => ['test1' => true, 'test2' => true],
                'flag' => 'test',
                'expectedResult' => [
                    'test' => true,
                    'test1' => true,
                    'test2' => true,

                ]
            ]
        ];
    }

    /**
     * Test isDoNotUseBaseCategoryFlagSet method
     *
     * @param array $flags
     * @param bool $expectedResult
     * @dataProvider isDoNotUseBaseCategoryFlagSetDataProvider
     * @throws \ReflectionException
     */
    public function testIsDoNotUseBaseCategoryFlagSet($flags, $expectedResult)
    {
        $this->setProperty('flags', $flags);

        $this->assertEquals($expectedResult, $this->model->isDoNotUseBaseCategoryFlagSet());
    }

    /**
     * @return array
     */
    public function isDoNotUseBaseCategoryFlagSetDataProvider()
    {
        return [
            [
                'flags' => [
                    State::DO_NOT_USE_BASE_CATEGORY => true
                ],
                'expectedResult' => true
            ],
            [
                'flags' => [
                    'test' => true
                ],
                'expectedResult' => false
            ],
            [
                'flags' => [],
                'expectedResult' => false
            ]
        ];
    }

    /**
     * Test setDoNotUseBaseCategoryFlag method
     *
     * @param array $flags
     * @param bool $expectedResult
     * @dataProvider setDoNotUseBaseCategoryFlagDataProvider
     * @throws \ReflectionException
     */
    public function testSetDoNotUseBaseCategoryFlag($flags, $expectedResult)
    {
        $this->assertEquals([], $this->getProperty('flags'));
        $this->setProperty('flags', $flags);

        $this->assertEquals($this->model, $this->model->setDoNotUseBaseCategoryFlag());
        $this->assertEquals($expectedResult, $this->getProperty('flags'));
    }

    /**
     * @return array
     */
    public function setDoNotUseBaseCategoryFlagDataProvider()
    {
        return [
            [
                'flags' => [],
                'expectedResult' => [
                    State::DO_NOT_USE_BASE_CATEGORY => true
                ]
            ],
            [
                'flags' => [
                    'test' => true
                ],
                'expectedResult' => [
                    State::DO_NOT_USE_BASE_CATEGORY => true,
                    'test' => true
                ]
            ],
        ];
    }

    /**
     * Test reset method
     */
    public function testReset()
    {
        $this->setProperty('flags', ['test3' => true]);

        $this->assertEquals($this->model, $this->model->reset());
        $this->assertEquals([], $this->getProperty('flags'));
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
