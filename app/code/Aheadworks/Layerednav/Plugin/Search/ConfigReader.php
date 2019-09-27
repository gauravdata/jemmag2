<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Plugin\Search;

use Aheadworks\Layerednav\Model\Search\Request\Container\Cleaner;
use Aheadworks\Layerednav\Model\Search\Request\Container\Duplicator;
use Magento\Framework\Config\ReaderInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ConfigReader
 * @package Aheadworks\Layerednav\Plugin\Search
 */
class ConfigReader
{
    /**
     * @var Cleaner
     */
    private $cleaner;

    /**
     * @var Duplicator
     */
    private $duplicator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $nativeContainerNames = ['catalog_view_container', 'quick_search_container'];

    /**
     * @param Cleaner $cleaner
     * @param Duplicator $duplicator
     * @param LoggerInterface $logger
     */
    public function __construct(
        Cleaner $cleaner,
        Duplicator $duplicator,
        LoggerInterface $logger
    ) {
        $this->cleaner = $cleaner;
        $this->duplicator = $duplicator;
        $this->logger = $logger;
    }

    /**
     * Fill additional containers
     *
     * @param ReaderInterface $subject
     * @param array $result
     * @param string $scope
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterRead(
        ReaderInterface $subject,
        array $result,
        $scope = null
    ) {
        try {
            foreach ($this->nativeContainerNames as $sourceName) {
                if (isset($result[$sourceName])) {
                    $destName = $sourceName . '_base';
                    $sourceContainer = $this->cleaner->perform($result[$sourceName]);
                    $result[$sourceName] = $sourceContainer;
                    $result[$destName] = $this->duplicator->perform(
                        $sourceContainer,
                        $sourceName,
                        $destName,
                        true
                    );
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $result;
    }
}
