<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Plugin;

use Aheadworks\Layerednav\Model\Search\Checker as SearchChecker;
use Aheadworks\Layerednav\Model\Search\Search as ExtendedSearch;
use Aheadworks\Layerednav\Plugin\Search;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Search\Api\SearchInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Plugin\Search
 */
class SearchTest extends TestCase
{
    /**
     * @var Search
     */
    private $plugin;

    /**
     * @var SearchChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCheckerMock;

    /**
     * @var ExtendedSearch|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extendedSearchMock;

    /**
     * Init mocks for tests
     *
     * @return void
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->searchCheckerMock = $this->createMock(SearchChecker::class);
        $this->extendedSearchMock = $this->createMock(ExtendedSearch::class);

        $this->plugin = $objectManager->getObject(
            Search::class,
            [
                'searchChecker' => $this->searchCheckerMock,
                'extendedSearch' => $this->extendedSearchMock
            ]
        );
    }

    /**
     * Test aroundSearch method
     *
     * @param bool $isExtendedSearchNeeded
     * @dataProvider aroundSearchDataProvider
     */
    public function testAroundSearch($isExtendedSearchNeeded)
    {
        $searchMock = $this->createMock(SearchInterface::class);
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $searchResultMock = $this->createMock(SearchResultInterface::class);

        $this->searchCheckerMock->expects($this->once())
            ->method('isExtendedSearchNeeded')
            ->willReturn($isExtendedSearchNeeded);

        if ($isExtendedSearchNeeded) {
            $this->extendedSearchMock->expects($this->once())
                ->method('search')
                ->with($searchCriteriaMock)
                ->willReturn($searchResultMock);
        } else {
            $this->extendedSearchMock->expects($this->never())
                ->method('search');
        }

        $proceed = function ($query) use ($searchCriteriaMock, $searchResultMock) {
            $this->assertEquals($searchCriteriaMock, $query);
            return $searchResultMock;
        };

        $this->assertSame(
            $searchResultMock,
            $this->plugin->aroundSearch(
                $searchMock,
                $proceed,
                $searchCriteriaMock
            )
        );
    }

    /**
     * @return array
     */
    public function aroundSearchDataProvider()
    {
        return [
            ['isExtendedSearchNeeded' => false],
            ['isExtendedSearchNeeded' => true]
        ];
    }
}
