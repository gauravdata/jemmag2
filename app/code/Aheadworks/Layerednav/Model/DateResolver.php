<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class DateResolver
 * @package Aheadworks\Layerednav\Model
 */
class DateResolver
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        TimezoneInterface $timezone
    ) {
        $this->timezone = $timezone;
    }

    /**
     * Get date in db format
     *
     * @param bool $currentTime
     * @param int $hour
     * @param int $minute
     * @param int $seconds
     * @return string
     */
    public function getDateInDbFormat($currentTime = true, $hour = 0, $minute = 0, $seconds = 0)
    {
        /** @var \DateTime $date */
        $date = $this->timezone->date();

        if (!$currentTime) {
            $date->setTime($hour, $minute, $seconds);
        }

        return $date->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
    }
}
