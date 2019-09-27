<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer;

use Aheadworks\Layerednav\Model\Layer\FilterList;
use Aheadworks\Layerednav\Model\Layer\FilterListResolver;
use Aheadworks\Layerednav\Model\PageTypeResolver;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\FilterListResolver
 */
class FilterListResolverTest extends TestCase
{
    const PAGE_TYPE1 = 'page_type1';
    const PAGE_TYPE2 = 'page_type2';
    const PAGE_TYPE_NOT_ASSIGNED = 'page_type_not_assigned';

    const FILTER_LIST_CLASS_NAME1 = 'FilterListClassName1';
    const FILTER_LIST_CLASS_NAME2 = 'FilterListClassName2';

    /**
     * @var FilterListResolver
     */
    private $filterListResolver;

    /**
     * @var ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManagerMock;

    /**
     * @var PageTypeResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $pageTypeResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->objectManagerMock = $this->createMock(ObjectManagerInterface::class);
        $this->pageTypeResolverMock = $this->createMock(PageTypeResolver::class);

        $this->filterListResolver = $objectManager->getObject(
            FilterListResolver::class,
            [
                'objectManager' => $this->objectManagerMock,
                'pageTypeResolver' => $this->pageTypeResolverMock,
                'filterListPool' => [
                    self::PAGE_TYPE1 => self::FILTER_LIST_CLASS_NAME1,
                    self::PAGE_TYPE2 => self::FILTER_LIST_CLASS_NAME2
                ]
            ]
        );
    }

    /**
     * Test create method
     *
     * @param string $pageType
     * @param string $filterListClassName
     * @dataProvider createDataProvider
     * @throws \Exception
     */
    public function testCreate($pageType, $filterListClassName)
    {
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with($filterListClassName);

        $this->filterListResolver->create($pageType);
    }

    /**
     * Test create method for current list
     */
    public function testCreateForCurrent()
    {
        $this->pageTypeResolverMock->expects($this->once())
            ->method('getType')
            ->willReturn(self::PAGE_TYPE1);
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(self::FILTER_LIST_CLASS_NAME1);

        $this->filterListResolver->create();
    }

    /**
     * Test create method if an exception occurs while filter list creation
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage page_type_not_assigned does not belong to any registered filter list
     */
    public function testCreateException()
    {
        $this->filterListResolver->create(self::PAGE_TYPE_NOT_ASSIGNED);
    }

    /**
     * Test create method if a filter list already created
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Filter List has been already created
     */
    public function testCreateExceptionAlreadyCreated()
    {
        $this->pageTypeResolverMock->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn(self::PAGE_TYPE1);

        $filterListMock = $this->createMock(FilterList::class);
        $this->objectManagerMock->expects($this->atLeastOnce())
            ->method('create')
            ->with(self::FILTER_LIST_CLASS_NAME1)
            ->willReturn($filterListMock);

        $this->filterListResolver->create();
        $this->filterListResolver->create();
    }

    /**
     * Test get method
     *
     * @throws \Exception
     */
    public function testGet()
    {
        $filterListMock = $this->getMockBuilder(FilterList::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->pageTypeResolverMock->expects($this->once())
            ->method('getType')
            ->willReturn(self::PAGE_TYPE1);
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(self::FILTER_LIST_CLASS_NAME1)
            ->willReturn($filterListMock);
        $this->assertEquals($filterListMock, $this->filterListResolver->get());
    }

    /**
     * @return array
     */
    public function createDataProvider()
    {
        return [
            'page type 1' => [self::PAGE_TYPE1, self::FILTER_LIST_CLASS_NAME1],
            'page type 2' => [self::PAGE_TYPE2, self::FILTER_LIST_CLASS_NAME2]
        ];
    }
}
