<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Applier;

/**
 * Class Pool
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Applier
 */
class Pool
{
    /**
     * @var ApplierInterface[]
     */
    private $appliers;

    /**
     * @param array $appliers
     */
    public function __construct(
        array $appliers = []
    ) {
        $this->appliers = $appliers;
    }

    /**
     * Get applier
     *
     * @param string $type
     * @return ApplierInterface
     * @throws \Exception
     */
    public function getApplier($type)
    {
        if (!isset($this->appliers[$type])) {
            throw new \Exception(sprintf('Unknown filter type: %s requested', $type));
        }
        $applier = $this->appliers[$type];
        if (!$applier instanceof ApplierInterface) {
            throw new \Exception(sprintf('Applier must implement %s interface', ApplierInterface::class));
        }

        return $applier;
    }
}
