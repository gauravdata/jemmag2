<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Filter;

use Aheadworks\Layerednav\Model\Layer\State as LayerState;

/**
 * Class Checker
 * @package Aheadworks\Layerednav\Model\Search\Filter
 */
class Checker
{
    /**
     * @var LayerState
     */
    private $layerState;

    /**
     * @param LayerState $layerState
     */
    public function __construct(
        LayerState $layerState
    ) {
        $this->layerState = $layerState;
    }

    /**
     * Check if has applied filters
     *
     * @return bool
     */
    public function hasAppliedFilters()
    {
        return count($this->layerState->getItems()) > 0;
    }

    /**
     * Get applied filters
     *
     * @return array ['filter_name' => ['field_one', ...], ...]
     */
    public function getAppliedFilters()
    {
        $appliedFilters = [];
        foreach ($this->layerState->getItems() as $item) {
            $field = $item->getFilterField();
            $condition = $item->getFilterCondition();
            $appliedFilters[$field] = $this->getFieldNames($field, $condition);
        }
        return $appliedFilters;
    }

    /**
     * Check if filter is applied
     *
     * @param string $field
     * @return bool
     */
    public function isApplied($field)
    {
        foreach ($this->getAppliedFilters() as $group => $names) {
            if ($field == $group || in_array($field, $names)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get extended filters
     *
     * @return array ['filter_name' => ['field_one', ...], ...]
     */
    public function getExtendedFilters()
    {
        $extendedFilters = [];
        foreach ($this->layerState->getItems() as $item) {
            if ($item->getFilterOrOption()) {
                $field = $item->getFilterField();
                $condition = $item->getFilterCondition();
                $extendedFilters[$field] = $this->getFieldNames($field, $condition);
            }
        }
        return $extendedFilters;
    }

    /**
     * Get field names
     *
     * @param string $field
     * @param array $condition
     * @return array
     */
    private function getFieldNames($field, $condition)
    {
        $names = [];
        if (in_array(key($condition), ['from', 'to'], true)) {
            if (isset($condition['from'])) {
                $names[] = $field . '.from';
            }
            if (isset($condition['to'])) {
                $names[] = $field . '.to';
            }
        } else {
            $names[] = $field;
        }

        return $names;
    }
}
