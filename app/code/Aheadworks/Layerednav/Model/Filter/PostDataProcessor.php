<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter;

/**
 * Class PostDataProcessor
 * @package Aheadworks\Layerednav\Model\Filter
 */
class PostDataProcessor implements PostDataProcessorInterface
{
    /**
     * @var PostDataProcessorInterface[]
     */
    private $processors;

    /**
     * @param PostDataProcessorInterface[] $processors
     */
    public function __construct(
        array $processors = []
    ) {
        $this->processors = $processors;
    }

    /**
     * Prepare entity data for save
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function process($data)
    {
        foreach ($this->processors as $processor) {
            if (!$processor instanceof PostDataProcessorInterface) {
                throw new \Exception(
                    sprintf(
                        'Processor instance %s does not implement PostDataProcessorInterface.',
                        get_class($processor)
                    )
                );
            }
            $data = $processor->process($data);
        }

        return $data;
    }
}
