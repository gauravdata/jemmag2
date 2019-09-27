<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Block;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Layerednav\Block\Navigation;
use Aheadworks\Layerednav\Model\Layer\Applier;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Layerednav\Block\Filter\Renderer as FilterRenderer;
use Magento\Catalog\Model\Layer;

/**
 * Test for \Aheadworks\Layerednav\Block\Navigation
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class NavigationTest extends TestCase
{
    /**
     * @var Navigation
     */
    private $block;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var LayerResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layerResolverMock;

    /**
     * @var Applier|\PHPUnit_Framework_MockObject_MockObject
     */
    private $applierMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->contextMock = $this->createMock(Context::class);
        $this->layerResolverMock = $this->createMock(LayerResolver::class);
        $this->applierMock = $this->createMock(Applier::class);

        $this->block = $objectManager->getObject(
            Navigation::class,
            [
                'context' => $this->contextMock,
                'layerResolver' => $this->layerResolverMock,
                'applier' => $this->applierMock,
            ]
        );
    }

    /**
     * Test _prepareLayout method
     */
    public function testPrepareLayout()
    {
        $layerMock = $this->createMock(Layer::class);

        $this->layerResolverMock->expects($this->once())
            ->method('get')
            ->willReturn($layerMock);

        $this->applierMock->expects($this->once())
            ->method('applyFilters')
            ->with($layerMock);

        $class = new \ReflectionClass($this->block);
        $method = $class->getMethod('_prepareLayout');
        $method->setAccessible(true);

        $this->assertSame($this->block, $method->invoke($this->block));
    }
}
