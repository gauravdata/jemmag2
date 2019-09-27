<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class MatcherPool
 * @package Aheadworks\Layerednav\App\Request
 */
class MatcherPool
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $matchers = [];

    /**
     * @var MatcherInterface[]
     */
    private $matcherInstances = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $matchers
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $matchers = []
    ) {
        $this->objectManager = $objectManager;
        $this->matchers = $matchers;
    }

    /**
     * Retrieves request matcher by type
     *
     * @param string $type
     * @return MatcherInterface
     * @throws \Exception
     */
    public function getMatcher($type)
    {
        if (!isset($this->matcherInstances[$type])) {
            if (!isset($this->matchers[$type])) {
                throw new \Exception(sprintf('Unknown matcher type: %s requested', $type));
            }
            $matcherInstance = $this->objectManager->create($this->matchers[$type]);
            if (!$matcherInstance instanceof MatcherInterface) {
                throw new \Exception(
                    sprintf('Matcher instance %s does not implement required interface.', $type)
                );
            }
            $this->matcherInstances[$type] = $matcherInstance;
        }
        return $this->matcherInstances[$type];
    }

    /**
     * Get matchers
     *
     * @return MatcherInterface[]
     * @throws \Exception
     */
    public function getMatchers()
    {
        foreach (array_keys($this->matchers) as $type) {
            $this->getMatcher($type);
        }
        return array_values($this->matcherInstances);
    }
}
