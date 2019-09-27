<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Customer;

use Aheadworks\Layerednav\Model\Customer\GroupResolver;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\Data\GroupSearchResultsInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Customer\GroupResolver
 */
class GroupResolverTest extends TestCase
{
    /**
     * @var GroupResolver
     */
    private $model;

    /**
     * @var GroupRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $groupRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->groupRepositoryMock = $this->createMock(GroupRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);

        $this->model = $objectManager->getObject(
            GroupResolver::class,
            [
                'groupRepository' => $this->groupRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock
            ]
        );
    }

    /**
     * Test getAllCustomerGroups method
     */
    public function testGetAllCustomerGroups()
    {
        $groupMock = $this->createMock(GroupInterface::class);
        $groups = [$groupMock];

        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $searchResultMock = $this->createMock(GroupSearchResultsInterface::class);
        $searchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn($groups);
        $this->groupRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);

        $this->assertEquals($groups, $this->model->getAllCustomerGroups());
    }

    /**
     * Test getAllCustomerGroups method if an error occurs
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Error!
     */
    public function testGetAllCustomerGroupsError()
    {
        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $this->groupRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willThrowException(new LocalizedException(__('Error!')));

        $this->model->getAllCustomerGroups();
    }

    /**
     * Test getAllCustomerGroupIds method
     */
    public function testGetAllCustomerGroupIds()
    {
        $groupOneMock = $this->getGroupMock(11);
        $groupTwoMock = $this->getGroupMock(22);
        $groups = [$groupOneMock, $groupTwoMock];
        $expectedResult = [11, 22];

        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $searchResultMock = $this->createMock(GroupSearchResultsInterface::class);
        $searchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn($groups);
        $this->groupRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);

        $this->assertEquals($expectedResult, $this->model->getAllCustomerGroupIds());
    }

    /**
     * Test getAllCustomerGroupIds method if an error occurs
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Error!
     */
    public function testGetAllCustomerGroupIdsError()
    {
        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $this->groupRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willThrowException(new LocalizedException(__('Error!')));

        $this->model->getAllCustomerGroupIds();
    }

    /**
     * Get group mock
     *
     * @param int $id
     * @return GroupInterface|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getGroupMock($id)
    {
        $groupMock = $this->createMock(GroupInterface::class);
        $groupMock->expects($this->any())
            ->method('getId')
            ->willReturn($id);

        return $groupMock;
    }
}
