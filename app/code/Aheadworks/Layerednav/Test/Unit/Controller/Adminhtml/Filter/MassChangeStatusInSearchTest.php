<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Controller\Adminhtml\Filter;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Controller\Adminhtml\Filter\MassChangeStatusInSearch;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\Collection as FilterCollection;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\CollectionFactory as FilterCollectionFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\App\RequestInterface;

/**
 * Test for \Aheadworks\Layerednav\Controller\Adminhtml\Filter\MassChangeStatusInSearch
 */
class MassChangeStatusInSearchTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MassChangeStatusInSearch
     */
    private $controller;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterMock;

    /**
     * @var RedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

    /**
     * @var FilterCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterCollectionFactoryMock;

    /**
     * @var FilterRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageManagerMock = $this->getMockBuilder(ManagerInterface::class)
            ->getMockForAbstractClass();
        $this->requestMock = $this->getMockBuilder(RequestInterface::class)
            ->getMockForAbstractClass();

        $this->contextMock = $objectManager->getObject(
            Context::class,
            [
                'messageManager' => $this->messageManagerMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock,
                'request' => $this->requestMock
            ]
        );

        $this->filterMock = $this->getMockBuilder(Filter::class)
            ->setMethods(['getCollection'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterCollectionFactoryMock = $this->getMockBuilder(FilterCollectionFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterRepositoryMock = $this->getMockBuilder(FilterRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->controller = $objectManager->getObject(
            MassChangeStatusInSearch::class,
            [
                'context' => $this->contextMock,
                'filter' => $this->filterMock,
                'filterCollectionFactory' => $this->filterCollectionFactoryMock,
                'filterRepository' => $this->filterRepositoryMock
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $filterIds = [1, 2];
        $filterCollectionSize = 2;
        $status = 1;

        $this->requestMock->expects($this->exactly(1))
            ->method('getParam')
            ->with('status')
            ->willReturn($status);

        $collectionMock = $this->getMockBuilder(FilterCollection::class)
            ->setMethods(['getAllIds'])
            ->disableOriginalConstructor()
            ->getMock();
        $collectionMock->expects($this->once())
            ->method('getAllIds')
            ->willReturn($filterIds);
        $this->filterCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->with($collectionMock)
            ->willReturn($collectionMock);

        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $filterMock->expects($this->exactly(2))
            ->method('setIsFilterableInSearch')
            ->with($status)
            ->willReturnSelf();

        $this->filterRepositoryMock->expects($this->exactly(2))
            ->method('get')
            ->willReturn($filterMock);
        $this->filterRepositoryMock->expects($this->exactly(2))
            ->method('save')
            ->with($filterMock)
            ->willReturn($filterMock);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('A total of %1 filter(s) have been updated.', $filterCollectionSize))
            ->willReturnSelf();

        $redirectMock = $this->getMockBuilder(Redirect::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $redirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($redirectMock);

        $this->assertSame($redirectMock, $this->controller->execute());
    }

    /**
     * Test execute method when no status specified
     */
    public function testExecuteNoFiltersSelected()
    {
        $this->requestMock->expects($this->exactly(1))
            ->method('getParam')
            ->with('status')
            ->willReturn(null);

        $redirectMock = $this->getMockBuilder(Redirect::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $redirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($redirectMock);

        $this->assertSame($redirectMock, $this->controller->execute());
    }

    /**
     * Test execute method if no filters can be updated
     */
    public function testExecuteNoFiltersCanBeUpdated()
    {
        $filterIds = [1, 2];
        $status = 1;

        $this->requestMock->expects($this->exactly(1))
            ->method('getParam')
            ->with('status')
            ->willReturn($status);

        $collectionMock = $this->getMockBuilder(FilterCollection::class)
            ->setMethods(['getAllIds'])
            ->disableOriginalConstructor()
            ->getMock();
        $collectionMock->expects($this->once())
            ->method('getAllIds')
            ->willReturn($filterIds);
        $this->filterCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->with($collectionMock)
            ->willReturn($collectionMock);

        $this->filterRepositoryMock->expects($this->exactly(2))
            ->method('get')
            ->willThrowException(new NoSuchEntityException());

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('None of selected filter(s) can be updated.'))
            ->willReturnSelf();

        $redirectMock = $this->getMockBuilder(Redirect::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $redirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($redirectMock);

        $this->assertSame($redirectMock, $this->controller->execute());
    }
}
