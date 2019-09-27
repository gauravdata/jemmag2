<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Plugin\Search;

use Aheadworks\Layerednav\Model\Search\Request\Container\Cleaner;
use Aheadworks\Layerednav\Model\Search\Request\Container\Duplicator;
use Aheadworks\Layerednav\Plugin\Search\ConfigReader;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Config\ReaderInterface;
use Psr\Log\LoggerInterface;

/**
 * Test for \Aheadworks\Layerednav\Plugin\Search\ConfigReader
 */
class ConfigReaderTest extends TestCase
{
    /**
     * @var ConfigReader
     */
    private $plugin;

    /**
     * @var Cleaner|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cleanerMock;

    /**
     * @var Duplicator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $duplicatorMock;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->cleanerMock = $this->createMock(Cleaner::class);
        $this->duplicatorMock = $this->createMock(Duplicator::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->plugin = $objectManager->getObject(
            ConfigReader::class,
            [
                'cleaner' => $this->cleanerMock,
                'duplicator' => $this->duplicatorMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    /**
     * Test afterRead method
     *
     * @param array $config
     * @param $cleanerMap
     * @param $duplicatorMap
     * @param array $expectedConfig
     * @dataProvider afterReadDataProvider
     */
    public function testAfterRead($config, $cleanerMap, $duplicatorMap, $expectedConfig)
    {
        $configReaderMock = $this->createMock(ReaderInterface::class);
        $scope = 1;

        if (!empty($cleanerMap)) {
            $this->cleanerMock->expects($this->atLeastOnce())
                ->method('perform')
                ->willReturnMap($cleanerMap);
        } else {
            $this->cleanerMock->expects($this->never())
                ->method('perform');
        }

        if (!empty($duplicatorMap)) {
            $this->duplicatorMock->expects($this->any())
                ->method('perform')
                ->willReturnMap($duplicatorMap);
        } else {
            $this->duplicatorMock->expects($this->never())
                ->method('perform');
        }

        $this->loggerMock->expects($this->never())
            ->method('critical');

        $this->assertEquals($expectedConfig, $this->plugin->afterRead($configReaderMock, $config, $scope));
    }

    /**
     * @return array
     */
    public function afterReadDataProvider()
    {
        return [
            [
                'config' => [
                    'quick_search_container' => ['container-data-1'],
                    'advanced_search_container' => ['container-data-2'],
                    'catalog_view_container' => ['container-data-3']
                ],
                'cleanerMap' => [
                    [['container-data-1'], ['cleaned-data-1']],
                    [['container-data-3'], ['cleaned-data-3']],
                ],
                'duplicatorMap' => [
                    [
                        ['cleaned-data-1'],
                        'quick_search_container',
                        'quick_search_container_base',
                        true,
                        ['duplicated-data-1']
                    ],
                    [
                        ['cleaned-data-3'],
                        'catalog_view_container',
                        'catalog_view_container_base',
                        true,
                        ['duplicated-data-3']
                    ],
                ],
                'expectedConfig' => [
                    'quick_search_container' => ['cleaned-data-1'],
                    'advanced_search_container' => ['container-data-2'],
                    'catalog_view_container' => ['cleaned-data-3'],
                    'catalog_view_container_base' => ['duplicated-data-3'],
                    'quick_search_container_base' => ['duplicated-data-1']
                ]
            ],
            [
                'config' => [
                    'advanced_search_container' => [],
                ],
                'cleanerMap' => [],
                'duplicatorMap' => [],
                'expectedConfig' => [
                    'advanced_search_container' => [],
                ]
            ],
            [
                'config' => [],
                'cleanerMap' => [],
                'duplicatorMap' => [],
                'expectedConfig' => []
            ]
        ];
    }

    /**
     * Test afterRead method if an error occurs (clean)
     */
    public function testAfterReadCleanError()
    {
        $configReaderMock = $this->createMock(ReaderInterface::class);
        $config = ['catalog_view_container' => ['container-data-1']];
        $scope = 1;
        $result = ['catalog_view_container' => ['container-data-1']];

        $this->cleanerMock->expects($this->once())
            ->method('perform')
            ->with(['container-data-1'])
            ->willThrowException(new \Exception('Error!'));

        $this->duplicatorMock->expects($this->never())
            ->method('perform');

        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with('Error!');

        $this->assertEquals($result, $this->plugin->afterRead($configReaderMock, $config, $scope));
    }

    /**
     * Test afterRead method if an error occurs (duplicate)
     */
    public function testAfterReadDuplicateError()
    {
        $configReaderMock = $this->createMock(ReaderInterface::class);
        $config = ['catalog_view_container' => ['container-data-1']];
        $scope = 1;
        $result = ['catalog_view_container' => ['cleaned-data-1']];

        $this->cleanerMock->expects($this->once())
            ->method('perform')
            ->with(['container-data-1'])
            ->willReturn(['cleaned-data-1']);

        $this->duplicatorMock->expects($this->once())
            ->method('perform')
            ->willThrowException(new \InvalidArgumentException('Error!'));

        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with('Error!');

        $this->assertEquals($result, $this->plugin->afterRead($configReaderMock, $config, $scope));
    }
}
