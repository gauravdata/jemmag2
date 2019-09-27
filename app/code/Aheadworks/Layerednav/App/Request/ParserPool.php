<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class ParserPool
 * @package Aheadworks\Layerednav\App\Request
 */
class ParserPool
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $parsers = [];

    /**
     * @var ParserInterface[]
     */
    private $parserInstances = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $parsers
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $parsers = []
    ) {
        $this->objectManager = $objectManager;
        $this->parsers = $parsers;
    }

    /**
     * Retrieves request parser by type
     *
     * @param string $type
     * @return ParserInterface
     * @throws \Exception
     */
    public function getParser($type)
    {
        if (!isset($this->parserInstances[$type])) {
            if (!isset($this->parsers[$type])) {
                throw new \Exception(sprintf('Unknown parser type: %s requested', $type));
            }
            $parserInstance = $this->objectManager->create($this->parsers[$type]);
            if (!$parserInstance instanceof ParserInterface) {
                throw new \Exception(
                    sprintf('Parser instance %s does not implement required interface.', $type)
                );
            }
            $this->parserInstances[$type] = $parserInstance;
        }
        return $this->parserInstances[$type];
    }
}
