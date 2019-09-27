<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Request\Container;

use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Search\Request\Container\Cleaner\CleanerInterface;

/**
 * Class Cleaner
 * @package Aheadworks\Layerednav\Model\Search\Request\Container
 */
class Cleaner
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CleanerInterface[]
     */
    private $cleaners;

    /**
     * @param Config $config
     * @param CleanerInterface[] $cleaners
     */
    public function __construct(
        Config $config,
        $cleaners = []
    ) {
        $this->config = $config;
        $this->cleaners = $cleaners;
    }

    /**
     * Clean not needed search container data
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function perform($data)
    {
        foreach ($this->cleaners as $filter => $cleaner) {
            if (!$cleaner instanceof CleanerInterface) {
                throw new \Exception('Cleaner must implement ' . CleanerInterface::class);
            }
            $data = $cleaner->perform($data, $filter);
        }

        return $data;
    }
}
