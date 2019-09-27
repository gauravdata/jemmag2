<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\App\Request\Parser;

use Aheadworks\Layerednav\App\Request\AttributeList;
use Aheadworks\Layerednav\App\Request\CategoryList;
use Aheadworks\Layerednav\App\Request\ParamDataProvider;
use Aheadworks\Layerednav\App\Request\Parser\ValueAsSubcategory;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\App\Request\Parser\ValueAsSubcategory
 */
class ValueAsSubcategoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ValueAsSubcategory
     */
    private $parser;

    /**
     * @var AttributeList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeListMock;

    /**
     * @var CategoryList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryListMock;

    /**
     * @var FilterManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterManagerMock;

    /**
     * @var array
     */
    private $attributesData = [
        'attribute1' => [
            ['value' => 1, 'label' => 'val11'],
            ['value' => 2, 'label' => 'val12'],
            ['value' => 3, 'label' => 'val11-val12']
        ]
    ];

    /**
     * @var array
     */
    private $decimalAttributeCodes = ['price', 'decimal'];

    /**
     * @var array
     */
    private $categoryUrlKeys = ['cat1', 'cat2', 'cat1-cat2', 'cat1-cat2_cat3'];

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->attributeListMock = $this->getMockBuilder(AttributeList::class)
            ->setMethods(['getAttributesKeyedByCode', 'getAttributeCodes'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->categoryListMock = $this->getMockBuilder(CategoryList::class)
            ->setMethods(['getCategoryUrlKeys'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterManagerMock = $this->getMockBuilder(FilterManager::class)
            ->setMethods(['__call'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->parser = $objectManager->getObject(
            ValueAsSubcategory::class,
            [
                'attributeList' => $this->attributeListMock,
                'categoryList' => $this->categoryListMock,
                'paramDataProvider' => $objectManager->getObject(ParamDataProvider::class),
                'filterManager' => $this->filterManagerMock
            ]
        );
    }

    /**
     * @param string $pathInfo
     * @param array $params
     * @param array $result
     * @dataProvider parseDataProvider
     */
    public function testParse($pathInfo, $params, $result)
    {
        /** @var Http|\PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->getMockBuilder(Http::class)
            ->setMethods(['getPathInfo', 'getParams'])
            ->disableOriginalConstructor()
            ->getMock();
        $requestMock->expects($this->once())
            ->method('getPathInfo')
            ->willReturn($pathInfo);
        $requestMock->expects($this->once())
            ->method('getParams')
            ->willReturn($params);

        $this->filterManagerMock->expects($this->any())
            ->method('__call')
            ->with('translitUrl')
            ->willReturnCallback([$this, 'returnFirstArgument']);

        $attributes = [];
        foreach ($this->attributesData as $attrCode => $optionsData) {
            $options = [];
            foreach ($optionsData as $optData) {
                $options[] = [
                    AttributeOptionInterface::VALUE => $optData['value'],
                    AttributeOptionInterface::LABEL => $optData['label'],
                ];
            }
            $attributes[$attrCode]['options'] = $options;
        }
        $this->attributeListMock->expects($this->once())
            ->method('getAttributesKeyedByCode')
            ->willReturn($attributes);
        $this->categoryListMock->expects($this->once())
            ->method('getCategoryUrlKeys')
            ->willReturn($this->categoryUrlKeys);
        $this->attributeListMock->expects($this->once())
            ->method('getAttributeCodes')
            ->with(AttributeList::LIST_TYPE_DECIMAL)
            ->willReturn($this->decimalAttributeCodes);

        $this->assertEquals($result, $this->parser->parse($requestMock));
    }

    /**
     * Returns first argument of magic method call
     *
     * @param string $method
     * @param array $args
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function returnFirstArgument($method, $args)
    {
        return $args[0];
    }

    /**
     * @return array
     */
    public function parseDataProvider()
    {
        return [
            [
                '/category.html/attribute1-val11-val12/category-cat1-cat2/'
                . 'price-5.00-10.00/decimal-1.00-5.00?param=value',
                [],
                [
                    'pathParams' => ['category.html'],
                    'filterParams' => [
                        'attribute1' => 'val11,val12',
                        'cat' => 'cat1,cat2',
                        'price' => '5.00-10.00',
                        'decimal' => '1.00-5.00'
                    ]
                ]
            ],
            [
                '/category.html/attribute1-val11-val12-val3/category-cat1-cat2-cat3/',
                [],
                [
                    'pathParams' => ['category.html'],
                    'filterParams' => [
                        'attribute1' => 'val11,val12',
                        'cat' => 'cat1,cat2'
                    ]
                ]
            ],
            [
                '/category.html/attribute1-val11--val12/category-cat1--cat2/',
                [],
                [
                    'pathParams' => ['category.html'],
                    'filterParams' => [
                        'attribute1' => 'val11-val12',
                        'cat' => 'cat1-cat2'
                    ]
                ]
            ],
            [
                '/catalog/category/view/id/1/attribute1-val11-val12/category-cat1-cat2?param=value',
                [],
                [
                    'pathParams' => ['catalog', 'category', 'view', 'id', '1'],
                    'filterParams' => [
                        'attribute1' => 'val11,val12',
                        'cat' => 'cat1,cat2'
                    ]
                ]
            ],
            [
                '/category.html',
                [],
                [
                    'pathParams' => ['category.html'],
                    'filterParams' => []
                ]
            ],
            [
                '/attribute1-val11/attribute1-val11/attribute1-val11',
                [],
                [
                    'pathParams' => ['attribute1-val11', 'attribute1-val11'],
                    'filterParams' => ['attribute1' => 'val11']
                ]
            ],
            [
                '/category-cat1/category-cat1/category-cat1',
                [],
                [
                    'pathParams' => ['category-cat1', 'category-cat1'],
                    'filterParams' => ['cat' => 'cat1']
                ]
            ],
            [
                '/category.html/in-stock/on-sale/new',
                [],
                [
                    'pathParams' => ['category.html'],
                    'filterParams' => [
                        'in-stock' => 1,
                        'on-sale' => 1,
                        'new' => 1
                    ]
                ]
            ],
            [
                '/in-stock/in-stock/in-stock',
                [],
                [
                    'pathParams' => ['in-stock', 'in-stock'],
                    'filterParams' => [
                        'in-stock' => 1
                    ]
                ]
            ],
            [
                '/category.html/price-5.00-10.00--20.00-30.00/decimal-1.00---5.00-6.00',
                [],
                [
                    'pathParams' => ['category.html'],
                    'filterParams' => [
                        'price' => '5.00-10.00,20.00-30.00',
                        'decimal' => '1.00-,5.00-6.00'
                    ]
                ]
            ],
            [
                '/category.html/attribute1-val11-val12/category-cat1-cat2/'
                . 'price-5.00-10.00/decimal-1.00-5.00?param=value',
                [
                    'id' => 21
                ],
                [
                    'pathParams' => ['category.html'],
                    'filterParams' => [
                        'attribute1' => 'val11,val12',
                        'cat' => 'cat1,cat2',
                        'price' => '5.00-10.00',
                        'decimal' => '1.00-5.00',
                        'parent_cat_id' => 21
                    ]
                ]
            ],
            [
                '/in-stock/in-stock/in-stock',
                [
                    'id' => 21
                ],
                [
                    'pathParams' => ['in-stock', 'in-stock'],
                    'filterParams' => [
                        'in-stock' => 1
                    ]
                ]
            ],
            [
                '/category.html',
                [
                    'id' => 21
                ],
                [
                    'pathParams' => ['category.html'],
                    'filterParams' => []
                ]
            ],
        ];
    }
}
