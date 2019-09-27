<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Magento\Config\Model\Config\Backend\Admin\Custom as BackendAdminCustom;

/**
 * Test for \Aheadworks\Layerednav\Model\Config
 */
class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->config = $objectManager->getObject(
            Config::class,
            ['scopeConfig' => $this->scopeConfigMock]
        );
    }

    /**
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsNewFilterEnabled($value)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_NEW_FILTER_ENABLED, ScopeInterface::SCOPE_STORE)
            ->willReturn($value);
        $this->assertSame($value, $this->config->isNewFilterEnabled());
    }

    /**
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsOnSaleFilterEnabled($value)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_ON_SALE_FILTER_ENABLED, ScopeInterface::SCOPE_STORE)
            ->willReturn($value);
        $this->assertSame($value, $this->config->isOnSaleFilterEnabled());
    }

    /**
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsInStockFilterEnabled($value)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_STOCK_FILTER_ENABLED, ScopeInterface::SCOPE_STORE)
            ->willReturn($value);
        $this->assertSame($value, $this->config->isInStockFilterEnabled());
    }

    /**
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsAjaxEnabled($value)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_AJAX_ENABLED)
            ->willReturn($value);
        $this->assertSame($value, $this->config->isAjaxEnabled());
    }

    /**
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsPopoverDisabled($value)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_POPOVER_DISABLED, ScopeInterface::SCOPE_STORE)
            ->willReturn($value);
        $this->assertSame($value, $this->config->isPopoverDisabled());
    }

    /**
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsPriceSliderEnabled($value)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_PRICE_SLIDER_ENABLED)
            ->willReturn($value);
        $this->assertSame($value, $this->config->isPriceSliderEnabled());
    }

    /**
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsPriceFromToEnabled($value)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_PRICE_FROM_TO_ENABLED)
            ->willReturn($value);
        $this->assertSame($value, $this->config->isPriceFromToEnabled());
    }

    /**
     * Test isManualFromToPriceFilterEnabled method
     *
     * @param bool $isSliderEnabled
     * @param bool $isFromToEnabled
     * @param bool $expectedResult
     * @dataProvider isManualFromToPriceFilterEnabledDataProvider
     */
    public function testIsManualFromToPriceFilterEnabled($isSliderEnabled, $isFromToEnabled, $expectedResult)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->willReturnMap(
                [
                    [
                        Config::XML_PATH_PRICE_SLIDER_ENABLED,
                        ScopeInterface::SCOPE_STORE,
                        null,
                        $isSliderEnabled
                    ],
                    [
                        Config::XML_PATH_PRICE_FROM_TO_ENABLED,
                        ScopeInterface::SCOPE_STORE,
                        null,
                        $isFromToEnabled
                    ],
                ]
            );

        $this->assertEquals($expectedResult, $this->config->isManualFromToPriceFilterEnabled());
    }

    /**
     * @return array
     */
    public function isManualFromToPriceFilterEnabledDataProvider()
    {
        return [
            [
                'isSliderEnabled' => false,
                'isFromToEnabled' => false,
                'expectedResult' => false
            ],
            [
                'isSliderEnabled' => true,
                'isFromToEnabled' => false,
                'expectedResult' => true
            ],
            [
                'isSliderEnabled' => false,
                'isFromToEnabled' => true,
                'expectedResult' => true
            ],
            [
                'isSliderEnabled' => true,
                'isFromToEnabled' => true,
                'expectedResult' => true
            ]
        ];
    }

    /**
     * Test getFilterDisplayState method
     *
     * @param int $value
     * @dataProvider filterStateDataProvider
     */
    public function testGetFilterDisplayState($value)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with(Config::XML_PATH_FILTER_DISPLAY_STATE, ScopeInterface::SCOPE_STORE)
            ->willReturn($value);
        $this->assertEquals($value, $this->config->getFilterDisplayState());
    }

    /**
     * @return array
     */
    public function filterStateDataProvider()
    {
        return [
            [FilterInterface::DISPLAY_STATE_EXPANDED, true],
            [FilterInterface::DISPLAY_STATE_COLLAPSED, false]
        ];
    }

    /**
     * Test getFilterValuesDisplayLimit method
     */
    public function testGetFilterValuesDisplayLimit()
    {
        $limitValue = "10";

        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with(Config::XML_PATH_FILTER_VALUES_DISPLAY_LIMIT, ScopeInterface::SCOPE_STORE)
            ->willReturn($limitValue);
        $this->assertSame((int)$limitValue, $this->config->getFilterValuesDisplayLimit());
    }

    /**
     * Test hideEmptyAttributeValues method
     *
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testHideEmptyAttributeValues($value)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_HIDE_EMPTY_ATTRIBUTE_VALUES, ScopeInterface::SCOPE_STORE)
            ->willReturn($value);
        $this->assertSame($value, $this->config->hideEmptyAttributeValues());
    }

    /**
     * Test getFilterMode method
     */
    public function testGetFilterMode()
    {
        $filterMode = 'single-select';

        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with(Config::XML_PATH_FILTER_MODE, ScopeInterface::SCOPE_STORE)
            ->willReturn($filterMode);
        $this->assertSame($filterMode, $this->config->getFilterMode());
    }

    /**
     * Test getSearchEngine method
     */
    public function testGetSearchEngine()
    {
        $value = "mysql";

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(BackendAdminCustom::XML_PATH_CATALOG_SEARCH_ENGINE)
            ->willReturn($value);

        $this->assertSame($value, $this->config->getSearchEngine());
    }

    /**
     * @return array
     */
    public function boolDataProvider()
    {
        return [[true], [false]];
    }
}
