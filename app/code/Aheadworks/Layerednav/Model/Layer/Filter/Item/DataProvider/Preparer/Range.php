<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer;

use Aheadworks\Layerednav\Model\Layer\Filter\Interval;
use Magento\Catalog\Model\Layer\Filter\Price\Render as PriceRenderer;

/**
 * Class Range
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer
 */
class Range
{
    /**
     * @var PriceRenderer
     */
    private $priceRenderer;

    /**
     * @param PriceRenderer $priceRenderer
     */
    public function __construct(
        PriceRenderer $priceRenderer
    ) {
        $this->priceRenderer = $priceRenderer;
    }

    /**
     * Prepare data.
     *
     * @param string $key
     * @param int $count
     * @param Interval|false $selectedInterval
     * @return array
     */
    public function prepareData($key, $count, $selectedInterval)
    {
        $from = $this->getFromValueByKey($key, $selectedInterval);
        $to = $this->getToValueByKey($key, $selectedInterval);

        $label = $this->priceRenderer->renderRangeLabel($from, $to);
        $value = $from . '-' . $to;

        $data = [
            'label' => $label,
            'value' => $value,
            'count' => $count,
            'from' => $from,
            'to' => $to,
        ];

        return $data;
    }

    /**
     * Retrieve prepared 'from' value
     *
     * @param string $key
     * @param Interval|false $selectedInterval
     * @return float|int
     */
    public function getFromValueByKey($key, $selectedInterval)
    {
        list($from, $to) = explode('_', $key);
        $from = $from == '*' ? $this->getFrom($to, $selectedInterval) : $this->getNumber($from);
        return $from;
    }

    /**
     * Retrieve prepared 'to' value
     *
     * @param string $key
     * @param Interval|false $selectedInterval
     * @return float|int
     */
    public function getToValueByKey($key, $selectedInterval)
    {
        list($from, $to) = explode('_', $key);
        $to = ($to == '*') ? $this->getTo($to, $selectedInterval) : $this->getNumber($to);
        return $to;
    }

    /**
     * Get number
     *
     * @param string $value
     * @return int|float
     */
    private function getNumber($value)
    {
        $int = intval($value);
        $float = floatval($value);
        $number = ($int == $float) ? $int : $float;

        return $number;
    }

    /**
     * Get 'from' part of the filter.
     *
     * @param float $from
     * @param Interval|false $selectedInterval
     * @return float
     */
    private function getFrom($from, $selectedInterval)
    {
        $fromPart = 0;
        if ($selectedInterval && is_numeric($selectedInterval->getFrom()) && $selectedInterval->getFrom() < $from) {
            $fromPart = $selectedInterval->getFrom();
        }
        return $fromPart;
    }

    /**
     * Get 'to' part of the filter.
     *
     * @param float $to
     * @param Interval|false $selectedInterval
     * @return float
     */
    private function getTo($to, $selectedInterval)
    {
        $toPart = '';
        if ($selectedInterval && is_numeric($selectedInterval->getTo()) && $selectedInterval->getTo() > $to) {
            $toPart = $selectedInterval->getTo();
        }
        return $toPart;
    }
}
