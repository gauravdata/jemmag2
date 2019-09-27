<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Controller\Adminhtml\Filter;

use Aheadworks\Layerednav\Controller\Adminhtml\Filter\MassSync;
use Aheadworks\Layerednav\Api\FilterManagementInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\Collection as FilterCollection;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\CollectionFactory as FilterCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\App\RequestInterface;

/**
 * Test for \Aheadworks\Layerednav\Controller\Adminhtml\Filter\MassSync
 */
class MassSyncTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MassSync
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
        $this->filterManagementMock = $this->getMockBuilder(FilterManagementInterface::class)
            ->getMockForAbstractClass();

        $this->controller = $objectManager->getObject(
            MassSync::class,
            [
                'context' => $this->contextMock,
                'filter' => $this->filterMock,
                'filterCollectionFactory' => $this->filterCollectionFactoryMock,
                'filterManagement' => $this->filterManagementMock
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

        $this->requestMock->expects($this->exactly(1))
            ->method('getParam')
            ->with(Filter::EXCLUDED_PARAM)
            ->willReturn(null);

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

        $this->filterManagementMock->expects($this->atLeastOnce())
            ->method('synchronizeFilterById')
            ->willReturn(true);

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
     * Test execute method when no filters can be updated
     */
    public function testExecuteNoFiltersUpdated()
    {
        $filterIds = [1, 2];

        $this->requestMock->expects($this->exactly(1))
            ->method('getParam')
            ->with(Filter::EXCLUDED_PARAM)
            ->willReturn(null);

        $collectionMock = $this->getMockBuilder(FilterCollection::class)
            ->setMethods(['getSize', 'getAllIds'])
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

        $this->filterManagementMock->expects($this->atLeastOnce())
            ->method('synchronizeFilterById')
            ->willReturn(false);

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
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

    /**
     * Test execute method when an exception occurs
     */
    public function testExecuteException()
    {
        $filterIds = [1, 2];

        $this->requestMock->expects($this->exactly(1))
            ->method('getParam')
            ->with(Filter::EXCLUDED_PARAM)
            ->willReturn(null);

        $collectionMock = $this->getMockBuilder(FilterCollection::class)
            ->setMethods(['getSize', 'getAllIds'])
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

        $this->filterManagementMock->expects($this->atLeastOnce())
            ->method('synchronizeFilterById')
            ->willThrowException(new NoSuchEntityException());

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('No such entity.'))
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
     * Test execute method when all items selected
     */
    public function testExecuteAllItemsSyncMode()
    {
        $this->requestMock->expects($this->exactly(1))
            ->method('getParam')
            ->with(Filter::EXCLUDED_PARAM)
            ->willReturn('false');

        $this->filterManagementMock->expects($this->once())
            ->method('synchronizeCustomFilters')
            ->willReturnSelf();
        $this->filterManagementMock->expects($this->once())
            ->method('synchronizeAttributeFilters')
            ->willReturnSelf();

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('All filters have been updated.'))
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
     * Test execute method when all items selected and an exception occurs
     */
    public function testExecuteAllItemsSyncModeException()
    {
        $this->requestMock->expects($this->exactly(1))
            ->method('getParam')
            ->with(Filter::EXCLUDED_PARAM)
            ->willReturn('false');

        $exception = new \Exception('Unknown error!!!');
        $this->filterManagementMock->expects($this->once())
            ->method('synchronizeCustomFilters')
            ->willThrowException($exception);

        $this->messageManagerMock->expects($this->once())
            ->method('addExceptionMessage')
            ->with($exception, __('Something went wrong while updating the filter(s).'))
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
