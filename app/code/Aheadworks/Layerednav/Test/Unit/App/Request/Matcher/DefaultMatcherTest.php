<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\App\Request\Matcher;

use Aheadworks\Layerednav\App\Request\AttributeList;
use Aheadworks\Layerednav\App\Request\Matcher\Base\PathMatcher;
use Aheadworks\Layerednav\App\Request\ParamDataProvider;
use Aheadworks\Layerednav\App\Request\Matcher\DefaultMatcher;
use Magento\Framework\App\RequestInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\App\Request\Matcher\DefaultMatcher
 */
class DefaultMatcherTest extends TestCase
{
    /**
     * @var DefaultMatcher
     */
    private $matcher;

    /**
     * @var PathMatcher|\PHPUnit_Framework_MockObject_MockObject
     */
    private $pathMatcherMock;

    /**
     * @var AttributeList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeListMock;

    /**
     * @var array
     */
    private $attributeCodes = ['attr1', 'attr2'];

    /**
     * @var array
     */
    private $decimalAttributeCodes = ['price', 'decimal'];

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->pathMatcherMock = $this->createMock(PathMatcher::class);
        $this->attributeListMock = $this->createMock(AttributeList::class);

        $this->matcher = $objectManager->getObject(
            DefaultMatcher::class,
            [
                'pathMatcher' => $this->pathMatcherMock,
                'attributeList' => $this->attributeListMock,
                'paramDataProvider' => $objectManager->getObject(ParamDataProvider::class)
            ]
        );
    }

    /**
     * @param bool $isPatchMatched
     * @param array $params
     * @param bool $result
     * @dataProvider matchParamsDataProvider
     * @throws \ReflectionException
     */
    public function testMatchParams($isPatchMatched, $params, $result)
    {
        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->expects($this->any())
            ->method('getParams')
            ->willReturn($params);

        $this->pathMatcherMock->expects($this->once())
            ->method('match')
            ->with($requestMock)
            ->willReturn($isPatchMatched);

        $this->attributeListMock->expects($this->any())
            ->method('getAttributeCodes')
            ->willReturnMap(
                [
                    [AttributeList::LIST_TYPE_DEFAULT, $this->attributeCodes],
                    [AttributeList::LIST_TYPE_DECIMAL, $this->decimalAttributeCodes]
                ]
            );

        $this->assertEquals($result, $this->matcher->match($requestMock));
    }

    /**
     * @return array
     */
    public function matchParamsDataProvider()
    {
        return [
            ['isPatchMatched' => true, 'params' => ['attr1' => '1'], 'result' => true],
            ['isPatchMatched' => true, 'params' => ['attr1' => '1,2'], 'result' => true],
            ['isPatchMatched' => true, 'params' => ['attr1' => 'value'], 'result' => false],
            ['isPatchMatched' => true, 'params' => ['attr1' => '1,value'], 'result' => false],
            ['isPatchMatched' => true, 'params' => ['price' => '10.00'], 'result' => true],
            ['isPatchMatched' => true, 'params' => ['price' => '10.00-20.00'], 'result' => true],
            ['isPatchMatched' => true, 'params' => ['cat' => '1'], 'result' => true],
            ['isPatchMatched' => true, 'params' => ['cat' => '1,2'], 'result' => true],
            ['isPatchMatched' => true, 'params' => [], 'result' => false],
            ['isPatchMatched' => true, 'params' => ['aw_new' => 1], 'result' => true],
            ['isPatchMatched' => true, 'params' => ['aw_new' => 'new'], 'result' => false],
            ['isPatchMatched' => true, 'params' => ['attr1' => ['1']], 'result' => false],
            ['isPatchMatched' => true, 'params' => ['attr1' => '1', 'attr2' => ['1']], 'result' => false],
            ['isPatchMatched' => true, 'params' => ['price' => ['1']], 'result' => false],
            ['isPatchMatched' => false, 'params' => ['attr1' => '1'], 'result' => false],
            ['isPatchMatched' => false, 'params' => ['price' => '10.00'], 'result' => false],
            ['isPatchMatched' => false, 'params' => ['cat' => '1'], 'result' => false],
            ['isPatchMatched' => false, 'params' => ['aw_new' => 1], 'result' => false],
        ];
    }
}
