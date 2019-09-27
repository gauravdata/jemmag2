<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Plugin;

use Aheadworks\Layerednav\Plugin\AttributeResource;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Aheadworks\Layerednav\Api\FilterManagementInterface;
use Magento\Catalog\Api\Data\EavAttributeInterface;
use Magento\Catalog\Model\ResourceModel\Attribute as AttributeResourceModel;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Plugin\AttributeResource
 */
class AttributeResourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AttributeResource
     */
    private $plugin;

    /**
     * @var AttributeResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeResourceModelMock;

    /**
     * @var FilterRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterRepositoryMock;

    /**
     * @var FilterManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterManagementMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->attributeResourceModelMock = $this->getMockBuilder(AttributeResourceModel::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterRepositoryMock = $this->getMockBuilder(FilterRepositoryInterface::class)
            ->getMockForAbstractClass();
        $this->filterManagementMock = $this->getMockBuilder(FilterManagementInterface::class)
            ->getMockForAbstractClass();

        $this->plugin = $objectManager->getObject(
            AttributeResource::class,
            [
                'filterRepository' => $this->filterRepositoryMock,
                'filterManagement' => $this->filterManagementMock
            ]
        );
    }

    /**
     * Test aroundSave method if a linked filter exists
     */
    public function testAroundSaveFilterExists()
    {
        $filterId = 1;
        $filterType = FilterInterface::ATTRIBUTE_FILTER;
        $attributeCode = 'color';

        $attributeMock = $this->createPartialMock(
            \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class,
            [
                'getData',
                'getAttributeCode',
            ]
        );

        $clousureCalled = false;
        $proceed = function ($query) use (&$clousureCalled, $attributeMock) {
            $clousureCalled = true;
            $this->assertEquals($attributeMock, $query);
            return $this->attributeResourceModelMock;
        };

        $this->filterManagementMock->expects($this->once())
            ->method('getAttributeFilterType')
            ->willReturn($filterType);

        $attributeMock->expects($this->once())
            ->method('getAttributeCode')
            ->willReturn($attributeCode);

        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();

        $this->filterRepositoryMock->expects($this->once())
            ->method('getByCode')
            ->with($attributeCode, $filterType)
            ->willReturn($filterMock);

        $this->filterManagementMock->expects($this->once())
            ->method('isSyncNeeded')
            ->with($filterMock, $attributeMock)
            ->willReturn(true);
        $this->filterManagementMock->expects($this->once())
            ->method('synchronizeFilter')
            ->with($filterMock, $attributeMock)
            ->willReturn(true);

        $this->assertEquals(
            $this->attributeResourceModelMock,
            $this->plugin->aroundSave(
                $this->attributeResourceModelMock,
                $proceed,
                $attributeMock
            )
        );
    }

    /**
     * Test aroundSave method if a linked filter does not exist
     */
    public function testAroundSaveFilterNotExists()
    {
        $filterType = FilterInterface::ATTRIBUTE_FILTER;
        $attributeCode = 'color';

        $attributeMock = $this->createPartialMock(
            \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class,
            [
                'getData',
                'getAttributeCode',
            ]
        );

        $clousureCalled = false;
        $proceed = function ($query) use (&$clousureCalled, $attributeMock) {
            $clousureCalled = true;
            $this->assertEquals($attributeMock, $query);
            return $this->attributeResourceModelMock;
        };

        $this->filterManagementMock->expects($this->once())
            ->method('getAttributeFilterType')
            ->willReturn($filterType);

        $attributeMock->expects($this->once())
            ->method('getAttributeCode')
            ->willReturn($attributeCode);

        $this->filterRepositoryMock->expects($this->once())
            ->method('getByCode')
            ->with($attributeCode, $filterType)
            ->willThrowException(new NoSuchEntityException());

        $this->filterManagementMock->expects($this->once())
            ->method('createFilter')
            ->with($attributeMock)
            ->willReturn(true);

        $this->assertEquals(
            $this->attributeResourceModelMock,
            $this->plugin->aroundSave(
                $this->attributeResourceModelMock,
                $proceed,
                $attributeMock
            )
        );
    }

    /**
     * Test aroundDelete method if a linked filter exists
     */
    public function testAroundDeleteFilterExists()
    {
        $filterId = 1;
        $filterType = FilterInterface::ATTRIBUTE_FILTER;
        $attributeCode = 'color';

        $attributeMock = $this->getMockBuilder(EavAttributeInterface::class)
            ->getMockForAbstractClass();

        $clousureCalled = false;
        $proceed = function ($query) use (&$clousureCalled, $attributeMock) {
            $clousureCalled = true;
            $this->assertEquals($attributeMock, $query);
            return $this->attributeResourceModelMock;
        };

        $this->filterManagementMock->expects($this->once())
            ->method('getAttributeFilterType')
            ->willReturn($filterType);

        $attributeMock->expects($this->once())
            ->method('getAttributeCode')
            ->willReturn($attributeCode);

        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();

        $this->filterRepositoryMock->expects($this->once())
            ->method('getByCode')
            ->with($attributeCode, $filterType)
            ->willReturn($filterMock);

        $filterMock->expects($this->once())
            ->method('getId')
            ->willReturn($filterId);

        $this->filterRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($filterMock)
            ->willReturn(true);

        $this->assertEquals(
            $this->attributeResourceModelMock,
            $this->plugin->aroundDelete(
                $this->attributeResourceModelMock,
                $proceed,
                $attributeMock
            )
        );
    }

    /**
     * Test aroundDelet method if a linked filter does not exist
     */
    public function testAroundDeleteFilterNotExists()
    {
        $filterType = FilterInterface::ATTRIBUTE_FILTER;
        $attributeCode = 'color';

        $attributeMock = $this->getMockBuilder(EavAttributeInterface::class)
            ->getMockForAbstractClass();

        $clousureCalled = false;
        $proceed = function ($query) use (&$clousureCalled, $attributeMock) {
            $clousureCalled = true;
            $this->assertEquals($attributeMock, $query);
            return $this->attributeResourceModelMock;
        };

        $this->filterManagementMock->expects($this->once())
            ->method('getAttributeFilterType')
            ->willReturn($filterType);

        $attributeMock->expects($this->once())
            ->method('getAttributeCode')
            ->willReturn($attributeCode);

        $this->filterRepositoryMock->expects($this->once())
            ->method('getByCode')
            ->with($attributeCode, $filterType)
            ->willThrowException(new NoSuchEntityException());

        $this->assertEquals(
            $this->attributeResourceModelMock,
            $this->plugin->aroundDelete(
                $this->attributeResourceModelMock,
                $proceed,
                $attributeMock
            )
        );
    }
}
