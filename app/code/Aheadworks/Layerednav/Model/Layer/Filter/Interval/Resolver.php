<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Interval;

use Aheadworks\Layerednav\Model\Layer\Filter\Interval;
use Aheadworks\Layerednav\Model\Layer\Filter\IntervalFactory;

/**
 * Class Resolver
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Interval
 */
class Resolver
{
    /**
     * @var IntervalFactory
     */
    private $intervalFactory;

    /**
     * @param IntervalFactory $intervalFactory
     */
    public function __construct(
        IntervalFactory $intervalFactory
    ) {
        $this->intervalFactory = $intervalFactory;
    }

    /**
     * Get interval
     *
     * @param string $filterData
     * @return Interval|false
     */
    public function getInterval($filterData)
    {
        $interval = false;
        if ($this->isIntervalValid($filterData)) {
            $intervalData = explode('-', $filterData);
            /** @var Interval $interval */
            $interval = $this->intervalFactory->create();
            $interval
                ->setFrom($intervalData[0])
                ->setTo($intervalData[1]);
        }
        return $interval;
    }

    /**
     * Check if interval is valid
     *
     * @param string $interval
     * @return bool
     */
    private function isIntervalValid($interval)
    {
        $interval = explode('-', $interval);
        if (is_array($interval) && count($interval) != 2) {
            return false;
        }
        foreach ($interval as $v) {
            if ($v !== '' && $v !== '0' && (double)$v <= 0 || is_infinite((double)$v)) {
                return false;
            }
        }
        return true;
    }
}
