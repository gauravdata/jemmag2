<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Template;

use Aheadworks\Layerednav\Model\Template\Filter;
use Magento\Framework\DataObject;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Template\Filter
 */
class FilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Filter
     */
    private $filter;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->filter = $objectManager->getObject(
            Filter::class,
            ['string' => $objectManager->getObject(StringUtils::class)]
        );
    }

    /**
     * @param string $value
     * @param array $vars
     * @param string $result
     * @dataProvider filterDataProvider
     */
    public function testFilter($value, $vars, $result)
    {
        $this->filter->setVariables($vars);
        $this->assertEquals($result, $this->filter->filter($value));
    }

    /**
     * @return array
     */
    public function filterDataProvider()
    {
        $objectManager = new ObjectManager($this);
        $objectVariable = $objectManager->getObject(
            DataObject::class,
            [
                'data' => [
                    'array' => [
                        $objectManager->getObject(
                            DataObject::class,
                            ['data' => ['name' => 'item name 1', 'value' => 'item value 1']]
                        ),
                        $objectManager->getObject(
                            DataObject::class,
                            ['data' => ['name' => 'item name 2', 'value' => 'item value 2']]
                        )
                    ]
                ]
            ]
        );
        return [
            [
                '{{for item in object.getArray()}}{{var item.name}} {{var item.value}}, {{/for}}',
                ['object' => $objectVariable],
                'item name 1 item value 1, item name 2 item value 2'
            ],
            [
                '{{for item in object.getArray()}}{{/for}}',
                ['object' => $objectVariable],
                ''
            ],
            [
                '{{for item in }}{{var item.name}} {{var item.value}}, {{/for}}',
                ['object' => $objectVariable],
                ''
            ],
            [
                '{{for item in object}}{{var item.name}} {{var item.value}}, {{/for}}',
                ['object' => $objectVariable],
                ''
            ]
        ];
    }
}
