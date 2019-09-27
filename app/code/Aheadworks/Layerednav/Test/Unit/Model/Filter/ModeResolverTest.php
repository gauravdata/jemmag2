<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter;

use Aheadworks\Layerednav\Api\Data\Filter\ModeInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\FilterExtensionInterface;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Filter\ModeResolver;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\ModeResolver
 */
class ModeResolverTest extends TestCase
{
    /**
     * @var ModeResolver
     */
    private $model;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createMock(Config::class);

        $this->model = $objectManager->getObject(
            ModeResolver::class,
            [
                'config' => $this->configMock,
            ]
        );
    }

    /**
     * Test getStorefrontFilterMode method
     *
     * @param string $type
     * @param string $storefrontFilterMode
     * @param string $configFilterMode
     * @param $singleSelectFilters
     * @param $multiSelectFilters
     * @param string $expectedResult
     * @dataProvider getStorefrontFilterModeDataProvider
     * @throws \ReflectionException
     */
    public function testGetStorefrontFilterMode(
        $type,
        $storefrontFilterMode,
        $configFilterMode,
        $singleSelectFilters,
        $multiSelectFilters,
        $expectedResult
    ) {
        $this->setProperty('singleSelectFilters', $singleSelectFilters);
        $this->setProperty('multiSelectFilters', $multiSelectFilters);

        $filterModeMock = $this->createMock(ModeInterface::class);
        $filterModeMock->expects($this->any())
            ->method('getStorefrontFilterMode')
            ->willReturn($storefrontFilterMode);

        $extensionAttributesMock = $this->createPartialMock(
            FilterExtensionInterface::class,
            [
                'getFilterMode',
                'setFilterMode',
                'getSwatches',
                'setSwatches',
                'getNativeVisualSwatches',
                'setNativeVisualSwatches'
            ]
        );
        $extensionAttributesMock->expects($this->any())
            ->method('getFilterMode')
            ->willReturn($filterModeMock);

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->any())
            ->method('getType')
            ->willReturn($type);
        $filterMock->expects($this->any())
            ->method('getExtensionAttributes')
            ->willReturn($extensionAttributesMock);

        $this->configMock->expects($this->any())
            ->method('getFilterMode')
            ->willReturn($configFilterMode);

        $this->assertEquals($expectedResult, $this->model->getStorefrontFilterMode($filterMock));
    }

    /**
     * @return array
     */
    public function getStorefrontFilterModeDataProvider()
    {
        return [
            [
                'type' => 'filter',
                'storefrontFilterMode' => null,
                'configFilterMode' => 'config-mode',
                'singleSelectFilters' => [],
                'multiSelectFilters' => [],
                'expectedResult' => 'config-mode'
            ],
            [
                'type' => 'filter',
                'storefrontFilterMode' => 'storefront-mode',
                'configFilterMode' => 'config-mode',
                'singleSelectFilters' => [],
                'multiSelectFilters' => [],
                'expectedResult' => 'storefront-mode'
            ],
            [
                'type' => 'filter',
                'storefrontFilterMode' => 'storefront-mode',
                'configFilterMode' => 'config-mode',
                'singleSelectFilters' => ['filter'],
                'multiSelectFilters' => [],
                'expectedResult' => ModeInterface::MODE_SINGLE_SELECT
            ],
            [
                'type' => 'filter',
                'storefrontFilterMode' => 'storefront-mode',
                'configFilterMode' => 'config-mode',
                'singleSelectFilters' => [],
                'multiSelectFilters' => ['filter'],
                'expectedResult' => ModeInterface::MODE_MULTI_SELECT
            ],
        ];
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
