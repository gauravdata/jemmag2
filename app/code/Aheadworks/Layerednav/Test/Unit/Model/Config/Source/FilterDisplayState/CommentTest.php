<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Config\Source\FilterDisplayState;

use Aheadworks\Layerednav\Model\Config\Source\FilterDisplayState\Comment;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;

/**
 * Test for \Aheadworks\Layerednav\Model\Config\Source\FilterDisplayState\Comment
 */
class CommentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Comment
     */
    private $model;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->urlBuilderMock = $this->getMockBuilder(UrlInterface::class)
            ->getMockForAbstractClass();

        $this->model = $objectManager->getObject(
            Comment::class,
            [
                'urlBuilder' => $this->urlBuilderMock
            ]
        );
    }

    /**
     * Test getCommentText method
     */
    public function testGetCommentText()
    {
        $elementValue = '2';
        $url = 'http://example.com/admin/aw_layerednav/filter/index/';

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('aw_layerednav/filter/index')
            ->willReturn($url);

        $this->assertTrue(is_string($this->model->getCommentText($elementValue)));
    }
}
