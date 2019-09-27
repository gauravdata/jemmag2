<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Controller\Adminhtml\Filter;

use Aheadworks\Layerednav\Controller\Adminhtml\Filter\Save;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterfaceFactory;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Aheadworks\Layerednav\Ui\FilterDataProvider;
use Aheadworks\Layerednav\Model\Filter\PostDataProcessor as FilterPostDataProcessor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Test for \Aheadworks\Layerednav\Controller\Adminhtml\Filter\Save
 */
class SaveTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Save
     */
    private $controller;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var PageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultPageFactoryMock;

    /**
     * @var RedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

    /**
     * @var FilterInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterFactoryMock;

    /**
     * @var FilterRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterRepositoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var DataPersistorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataPersistorMock;

    /**
     * @var FilterPostDataProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterPostDataProcessorMock;

    /**
     * @var array
     */
    private $formData = [
        'id' => 1,
        'title' => 'Color',
        'code' => 'color',
        'type' => FilterInterface::ATTRIBUTE_FILTER,
        'is_filterable' => '1',
        'is_filterable_in_search' => '1',
        'position' => '10',
        'store_id' => '1',
        'display_state' => FilterInterface::DISPLAY_STATE_COLLAPSED,
        'default_display_state' => '0'
    ];

    /**
     * @var array
     */
    private $preparedData = [
        'id' => 1,
        'title' => 'Color',
        'code' => 'color',
        'type' => FilterInterface::ATTRIBUTE_FILTER,
        'is_filterable' => '1',
        'is_filterable_in_search' => '1',
        'position' => '10',
        'store_id' => '1',
        'display_state' => FilterInterface::DISPLAY_STATE_COLLAPSED,
        'default_display_state' => '0',
        'display_states' => [
            [
                'store_id' => '1',
                'value' => FilterInterface::DISPLAY_STATE_COLLAPSED
            ]
        ]
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->requestMock = $this->getMockBuilder(RequestInterface::class)
            ->setMethods(['getPostValue'])
            ->getMockForAbstractClass();
        $this->messageManagerMock = $this->getMockBuilder(ManagerInterface::class)
            ->getMockForAbstractClass();
        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'messageManager' => $this->messageManagerMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock
            ]
        );

        $this->resultPageFactoryMock = $this->getMockBuilder(PageFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterFactoryMock = $this->getMockBuilder(FilterInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterRepositoryMock = $this->getMockBuilder(FilterRepositoryInterface::class)
            ->getMockForAbstractClass();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->setMethods(['populateWithArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataPersistorMock = $this->getMockBuilder(DataPersistorInterface::class)
            ->getMockForAbstractClass();
        $this->filterPostDataProcessorMock = $this->getMockBuilder(FilterPostDataProcessor::class)
            ->setMethods(['process'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = $objectManager->getObject(
            Save::class,
            [
                'context' => $this->contextMock,
                'resultPageFactory' => $this->resultPageFactoryMock,
                'filterFactory' => $this->filterFactoryMock,
                'filterRepository' => $this->filterRepositoryMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataPersistor' => $this->dataPersistorMock,
                'filterPostDataProcessor' => $this->filterPostDataProcessorMock
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $preparedData = $this->preparedData;

        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn($this->formData);

        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $this->filterRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->formData['id'])
            ->willReturn($filterMock);

        $this->filterPostDataProcessorMock->expects($this->once())
            ->method('process')
            ->with($this->formData)
            ->willReturn($this->preparedData);

        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($filterMock, $preparedData)
            ->willReturn($filterMock);

        $this->filterRepositoryMock->expects($this->once())
            ->method('save')
            ->with($filterMock)
            ->willReturn($filterMock);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('Filter was successfully saved.'))
            ->willReturnSelf();

        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()
            ->setMethods(['setPath'])
            ->getMock();
        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->controller->execute());
    }

    /**
     * Testing of execute method, if an error occurs
     *     */
    public function testExecuteErrorOccurs()
    {
        $exception = new \Exception;
        $preparedData = $this->preparedData;

        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn($this->formData);

        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $this->filterRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->formData['id'])
            ->willReturn($filterMock);

        $this->filterPostDataProcessorMock->expects($this->once())
            ->method('process')
            ->with($this->formData)
            ->willReturn($this->preparedData);

        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($filterMock, $preparedData)
            ->willReturn($filterMock);

        $this->filterRepositoryMock->expects($this->once())
            ->method('save')
            ->with($filterMock)
            ->willThrowException($exception);

        $this->messageManagerMock->expects($this->once())
            ->method('addExceptionMessage')
            ->with($exception, __('Something went wrong while saving the filter data.'))
            ->willReturnSelf();

        $this->dataPersistorMock->expects($this->once())
            ->method('set')
            ->with(FilterDataProvider::FILTER_PERSISTOR_KEY, $this->formData)
            ->willReturnSelf();

        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()
            ->setMethods(['setPath'])
            ->getMock();
        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/edit')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->controller->execute());
    }

    /**
     * Testing of execute method if no data posted
     */
    public function testExecuteNoData()
    {
        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn(null);

        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()
            ->setMethods(['setPath'])
            ->getMock();
        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->controller->execute());
    }
}
