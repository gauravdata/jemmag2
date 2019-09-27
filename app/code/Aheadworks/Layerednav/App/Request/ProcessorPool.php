<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class ProcessorPool
 * @package Aheadworks\Layerednav\App\Request
 */
class ProcessorPool
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $processors = [];

    /**
     * @var ProcessorInterface[]
     */
    private $processorInstances = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $processors
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $processors = []
    ) {
        $this->objectManager = $objectManager;
        $this->processors = $processors;
    }

    /**
     * Retrieves request processor by type
     *
     * @param string $type
     * @return ProcessorInterface
     * @throws \Exception
     */
    public function getProcessor($type)
    {
        if (!isset($this->processorInstances[$type])) {
            if (!isset($this->processors[$type])) {
                throw new \Exception(sprintf('Unknown processor type: %s requested', $type));
            }
            $processorInstance = $this->objectManager->create($this->processors[$type]);
            if (!$processorInstance instanceof ProcessorInterface) {
                throw new \Exception(
                    sprintf('Processor instance %s does not implement required interface.', $type)
                );
            }
            $this->processorInstances[$type] = $processorInstance;
        }
        return $this->processorInstances[$type];
    }
}
