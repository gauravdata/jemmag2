<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\App\Request\Parser;

use Aheadworks\Layerednav\App\Request\AttributeList;
use Aheadworks\Layerednav\App\Request\CategoryList;
use Aheadworks\Layerednav\App\Request\ParamDataProvider;
use Aheadworks\Layerednav\App\Request\Parser\ValueInsteadOfId;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\App\Request\Parser\ValueInsteadOfId
 */
class ValueInsteadOfIdTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ValueInsteadOfId
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
    private $categoryUrlKeys = ['cat1', 'cat2', 'cat1-cat2'];

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
            ValueInsteadOfId::class,
            [
                'attributeList' => $this->attributeListMock,
                'categoryList' => $this->categoryListMock,
                'paramDataProvider' => $objectManager->getObject(ParamDataProvider::class),
                'filterManager' => $this->filterManagerMock
            ]
        );
    }

    /**
     * @param string $params
     * @param array $result
     * @dataProvider parseDataProvider
     */
    public function testParse($params, $result)
    {
        /** @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->getMockForAbstractClass(RequestInterface::class);

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
        $this->attributeListMock->expects($this->once())
            ->method('getAttributeCodes')
            ->with(AttributeList::LIST_TYPE_DECIMAL)
            ->willReturn($this->decimalAttributeCodes);
        if (isset($params['cat'])) {
            $this->categoryListMock->expects($this->once())
                ->method('getCategoryUrlKeys')
                ->willReturn($this->categoryUrlKeys);
        }

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
                ['attribute1' => 'val11'],
                [
                    'filterParams' => ['attribute1' => 'val11']
                ]
            ],
            [
                ['attribute1' => 'val13'],
                [
                    'filterParams' => []
                ]
            ],
            [
                ['attribute1' => 'val11-val12-val11--val12'],
                [
                    'filterParams' => ['attribute1' => 'val11,val12,val11-val12']
                ]
            ],
            [
                ['attribute1' => 'val11-val12-val11--val13'],
                [
                    'filterParams' => ['attribute1' => 'val11,val12']
                ]
            ],
            [
                ['cat' => 'cat1'],
                [
                    'filterParams' => ['cat' => 'cat1']
                ]
            ],
            [
                ['cat' => 'cat1-cat2-cat1--cat2'],
                [
                    'filterParams' => ['cat' => 'cat1,cat2,cat1-cat2']
                ]
            ],
            [
                ['cat' => 'cat1-cat2-cat1--cat3'],
                [
                    'filterParams' => ['cat' => 'cat1,cat2']
                ]
            ],
            [
                ['aw_stock' => 'in_stock'],
                [
                    'filterParams' => ['aw_stock' => 1]
                ]
            ],
            [
                [
                    'attribute1' => 'val11',
                    'param' => 'param-value',
                    'cat' => 'cat1',
                    'aw_stock' => 'in_stock'
                ],
                [
                    'filterParams' => [
                        'attribute1' => 'val11',
                        'cat' => 'cat1',
                        'aw_stock' => 1
                    ]
                ]
            ],
            [
                ['price' => '10.00-20.00'],
                ['filterParams' => ['price' => '10.00-20.00']]
            ],
            [
                ['price' => '10.00-20.00--30.00-40.00'],
                ['filterParams' => ['price' => '10.00-20.00,30.00-40.00']]
            ],
            [
                ['price' => '10.00-20.00--30.00-'],
                ['filterParams' => ['price' => '10.00-20.00,30.00-']]
            ],
            [
                ['price' => '30.00---10.00-20.00'],
                ['filterParams' => ['price' => '30.00-,10.00-20.00']]
            ],
            [
                [
                    'id' => 21,
                    'cat' => 'cat1'
                ],
                [
                    'filterParams' => ['cat' => 'cat1', 'parent_cat_id' => 21]
                ]
            ],
            [
                [
                    'id' => 21,
                    'attribute1' => 'val11'
                ],
                [
                    'filterParams' => ['attribute1' => 'val11']
                ]
            ],
            [
                [
                    'id' => 21,
                    'attribute1' => 'val11',
                    'param' => 'param-value',
                    'cat' => 'cat1',
                    'aw_stock' => 'in_stock'
                ],
                [
                    'filterParams' => [
                        'attribute1' => 'val11',
                        'cat' => 'cat1',
                        'aw_stock' => 1,
                        'parent_cat_id' => 21,
                    ]
                ]
            ],
        ];
    }
}
