<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter\Swatch;

use Aheadworks\Layerednav\Model\Filter\Swatch\Finder;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Layerednav\Model\Filter\Swatch\Repository as SwatchRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\Swatch\Finder
 */
class FinderTest extends TestCase
{
    /**
     * @var Finder
     */
    private $model;

    /**
     * @var SwatchRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $swatchRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->swatchRepositoryMock = $this->createMock(SwatchRepository::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);

        $this->model = $objectManager->getObject(
            Finder::class,
            [
                'swatchRepository' => $this->swatchRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
            ]
        );
    }

    /**
     * Test getByFilterId method
     */
    public function testGetByFilterId()
    {
        $filterId = 12;
        $swatch = $this->createMock(SwatchInterface::class);
        $result = [
            $swatch
        ];

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(SwatchInterface::FILTER_ID, $filterId)
            ->willReturnSelf();

        $searchCriteria = $this->createMock(SearchCriteria::class);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $this->swatchRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($result);

        $this->assertSame($result, $this->model->getByFilterId($filterId));
    }

    /**
     * Test getByFilterId method with exception
     */
    public function testGetByFilterIdException()
    {
        $filterId = 12;
        $result = [];

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(SwatchInterface::FILTER_ID, $filterId)
            ->willReturnSelf();

        $searchCriteria = $this->createMock(SearchCriteria::class);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $this->swatchRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteria)
            ->willThrowException(new NoSuchEntityException());

        $this->assertSame($result, $this->model->getByFilterId($filterId));
    }

    /**
     * Test getByOptionId method
     */
    public function testGetByOptionId()
    {
        $optionId = 12;
        $swatch = $this->createMock(SwatchInterface::class);
        $result = [
            $swatch
        ];

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(SwatchInterface::OPTION_ID, $optionId)
            ->willReturnSelf();

        $searchCriteria = $this->createMock(SearchCriteria::class);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $this->swatchRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($result);

        $this->assertSame($swatch, $this->model->getByOptionId($optionId));
    }

    /**
     * Test getByOptionId method with exception
     */
    public function testGetByOptionIdException()
    {
        $optionId = 12;
        $result = null;

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(SwatchInterface::OPTION_ID, $optionId)
            ->willReturnSelf();

        $searchCriteria = $this->createMock(SearchCriteria::class);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $this->swatchRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteria)
            ->willThrowException(new NoSuchEntityException());

        $this->assertSame($result, $this->model->getByOptionId($optionId));
    }
}
