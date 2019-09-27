<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Controller\Ajax;

use Aheadworks\Layerednav\Controller\Ajax\ItemsCount;
use Aheadworks\Layerednav\Model\Layer\Applier;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Config\Source\SeoFriendlyUrl;
use Aheadworks\Layerednav\Model\Layer\FilterListResolver;
use Aheadworks\Layerednav\Model\PageTypeResolver;
use Aheadworks\Layerednav\Model\Url\ConverterPool;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Search\Model\QueryFactory;

/**
 * Test for \Aheadworks\Layerednav\Controller\Ajax\ItemsCount
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ItemsCountTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ItemsCount
     */
    private $action;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Resolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layerResolverMock;

    /**
     * @var PageTypeResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $pageTypeResolverMock;

    /**
     * @var FilterListResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterListResolverMock;

    /**
     * @var Applier|\PHPUnit_Framework_MockObject_MockObject
     */
    private $applierMock;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var ConverterPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlConverterPoolMock;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultFactoryMock;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->layerResolverMock = $this->getMockBuilder(Resolver::class)
            ->setMethods(['create', 'get'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageTypeResolverMock = $this->getMockBuilder(PageTypeResolver::class)
            ->setMethods(['getLayerType'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterListResolverMock = $this->getMockBuilder(FilterListResolver::class)
            ->setMethods(['create', 'get'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->applierMock = $this->getMockBuilder(Applier::class)
            ->setMethods(['applyFilters'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->configMock = $this->getMockBuilder(Config::class)
            ->setMethods(['getSeoFriendlyUrlOption'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->urlConverterPoolMock = $this->getMockBuilder(ConverterPool::class)
            ->setMethods(['getConverter'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock = $this->getMockBuilder(Context::class)
            ->setMethods(['getRequest', 'getRedirect', 'getResultFactory'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->requestMock);
        $this->contextMock->expects($this->any())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock);

        $this->action = $this->objectManager->getObject(
            ItemsCount::class,
            [
                'context' => $this->contextMock,
                'layerResolver' => $this->layerResolverMock,
                'pageTypeResolver' => $this->pageTypeResolverMock,
                'filterListResolver' => $this->filterListResolverMock,
                'applier' => $this->applierMock,
                'config' => $this->configMock,
                'urlConverterPool' => $this->urlConverterPoolMock
            ]
        );
    }

    /**
     * @param array $requestParams
     * @dataProvider executeDataProvider
     */
    public function testExecute($requestParams)
    {
        $itemsCount = 10;

        $resultJsonMock = $this->getMockBuilder(Json::class)
            ->setMethods(['setData'])
            ->disableOriginalConstructor()
            ->getMock();
        $layerMock = $this->getMockBuilder(Layer::class)
            ->setMethods(['getProductCollection', 'setCurrentCategory'])
            ->disableOriginalConstructor()
            ->getMock();
        $productCollectionMock = $this->getMockBuilder(Collection::class)
            ->setMethods(['getSize'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($resultJsonMock);
        $this->requestMock->expects($this->once())
            ->method('getParams')
            ->willReturn($requestParams);
        $this->configMock->expects($this->once())
            ->method('getSeoFriendlyUrlOption')
            ->willReturn(SeoFriendlyUrl::DEFAULT_OPTION);
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['pageType', null, $requestParams['pageType']],
                    ['categoryId', null, $requestParams['categoryId']],
                    ['searchQueryText', null, $requestParams['searchQueryText']],
                    ['sequence', null, $requestParams['sequence']]
                ]
            );
        $this->requestMock->expects($this->once())
            ->method('setParams')
            ->with(
                $this->callback(
                    function ($params) use ($requestParams) {
                        $filterValueKey = $requestParams['filterValue'][0]['key'];
                        $filterValue = $requestParams['filterValue'][0]['value'];

                        $isValid = array_key_exists($filterValueKey, $params)
                            && $params[$filterValueKey] == $filterValue;
                        if ($requestParams['pageType'] == 'catalog_search') {
                            $isValid = $isValid && array_key_exists(QueryFactory::QUERY_VAR_NAME, $params)
                                && $params[QueryFactory::QUERY_VAR_NAME] == $requestParams['searchQueryText'];
                        }

                        return $isValid;
                    }
                )
            );
        $this->filterListResolverMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo($requestParams['pageType']));
        $this->layerResolverMock->expects($this->once())
            ->method('get')
            ->willReturn($layerMock);
        $this->applierMock->expects($this->once())
            ->method('applyFilters')
            ->with($this->equalTo($layerMock));
        $layerMock->expects($this->once())
            ->method('getProductCollection')
            ->willReturn($productCollectionMock);
        $productCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($itemsCount);
        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->with(
                $this->equalTo(
                    [
                        'success' => true,
                        'sequence' => $requestParams['sequence'],
                        'itemsCount' => $itemsCount,
                        'itemsContent' => $itemsCount . ' items'
                    ]
                )
            )
            ->willReturnSelf();

        $this->assertEquals($resultJsonMock, $this->action->execute());
    }

    public function testPrepareFilterValue()
    {
        $filterValue = [
            ['key' => 'filter_request_var1', 'value' => 'filter_value1'],
            ['key' => 'filter_request_var1', 'value' => 'filter_value2'],
            ['key' => 'filter_request_var2', 'value' => 'filter_value3']
        ];
        $preparedFilterValue = [
            'filter_request_var1' => 'filter_value1,filter_value2',
            'filter_request_var2' => 'filter_value3'
        ];

        $this->configMock->expects($this->once())
            ->method('getSeoFriendlyUrlOption')
            ->willReturn(SeoFriendlyUrl::DEFAULT_OPTION);

        $class = new \ReflectionClass($this->action);
        $method = $class->getMethod('prepareFilterValue');
        $method->setAccessible(true);

        $this->assertEquals($preparedFilterValue, $method->invokeArgs($this->action, [$filterValue]));
    }

    public function testGetLayer()
    {
        $pageType = 'category';
        $layerType = 'category';
        $categoryId = 1;

        $layerMock = $this->getMockBuilder(Layer::class)
            ->setMethods(['setCurrentCategory'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock->expects($this->exactly(2))
            ->method('getParam')
            ->willReturnMap(
                [
                    ['pageType', null, $pageType],
                    ['categoryId', null, $categoryId]
                ]
            );
        $this->pageTypeResolverMock->expects($this->once())
            ->method('getLayerType')
            ->with($pageType)
            ->willReturn($layerType);
        $this->layerResolverMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo($layerType));
        $this->layerResolverMock->expects($this->once())
            ->method('get')
            ->willReturn($layerMock);
        $layerMock->expects($this->once())
            ->method('setCurrentCategory')
            ->with($this->equalTo($categoryId));

        $class = new \ReflectionClass($this->action);
        $method = $class->getMethod('getLayer');
        $method->setAccessible(true);

        $this->assertSame($layerMock, $method->invoke($this->action));
    }

    /**
     * @return array
     */
    public function executeDataProvider()
    {
        return [
            'category page' => [
                [
                    'isAjax' => true,
                    'filterValue' => [['key' => 'filter_request_var', 'value' => 'filter_value']],
                    'pageType' => PageTypeResolver::PAGE_TYPE_CATEGORY,
                    'categoryId' => 1,
                    'searchQueryText' => '',
                    'sequence' => 1
                ]
            ],
            'search page' => [
                [
                    'isAjax' => true,
                    'filterValue' => [['key' => 'filter_request_var', 'value' => 'filter_value']],
                    'pageType' => PageTypeResolver::PAGE_TYPE_CATALOG_SEARCH,
                    'categoryId' => 1,
                    'searchQueryText' => 'search text',
                    'sequence' => 1
                ]
            ]
        ];
    }
}
