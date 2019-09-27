<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\Checker as FilterItemChecker;

/**
 * Class Base
 *
 * @package Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer
 */
class Base implements ArgumentInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var FilterItemChecker
     */
    protected $filterItemChecker;

    /**
     * @param Config $config
     * @param FilterItemChecker $filterItemChecker
     */
    public function __construct(
        Config $config,
        FilterItemChecker $filterItemChecker
    ) {
        $this->config = $config;
        $this->filterItemChecker = $filterItemChecker;
    }

    /**
     * Check if filter item is active
     *
     * @param FilterItemInterface $item
     * @return bool
     */
    public function isActiveItem(FilterItemInterface $item)
    {
        return $this->filterItemChecker->isActive($item);
    }

    /**
     * Check if need to to show item
     *
     * @param FilterItemInterface $item
     * @return bool
     */
    public function isNeedToShowItem(FilterItemInterface $item)
    {
        return !$this->config->hideEmptyAttributeValues()
            || ($this->isActiveItem($item))
            || ($item->getCount());
    }

    /**
     * Get filter items count to display
     *
     * @param FilterItemInterface[] $filterItems
     * @return int
     */
    public function getCountOfItemsToDisplay($filterItems)
    {
        $count = 0;
        foreach ($filterItems as $filterItem) {
            if ($this->isNeedToShowItem($filterItem)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get count of hidden items
     *
     * @param int $countOfItemsToDisplay
     * @return int
     */
    public function getHiddenItemsCount($countOfItemsToDisplay)
    {
        $displayLimit = $this->getDisplayLimit();
        if (isset($displayLimit) && $displayLimit > 0 && $countOfItemsToDisplay > $displayLimit) {
            return $countOfItemsToDisplay - $displayLimit;
        }
        return 0;
    }

    /**
     * Check if item on specific index is hidden
     *
     * @param int $itemIndex
     * @param int $countOfItemsToDisplay
     * @return bool
     */
    public function isItemHidden($itemIndex, $countOfItemsToDisplay)
    {
        return $this->getHiddenItemsCount($countOfItemsToDisplay) > 0
            && $itemIndex > $this->getDisplayLimit();
    }

    /**
     * Check if item on specific index is shaded
     *
     * @param int $itemIndex
     * @param int $countOfItemsToDisplay
     * @return bool
     */
    public function isItemShaded($itemIndex, $countOfItemsToDisplay)
    {
        return $this->getHiddenItemsCount($countOfItemsToDisplay) > 0
            && $itemIndex == $this->getDisplayLimit();
    }

    /**
     * Get filter values display limit
     *
     * @return int
     */
    protected function getDisplayLimit()
    {
        return $this->config->getFilterValuesDisplayLimit();
    }
}
