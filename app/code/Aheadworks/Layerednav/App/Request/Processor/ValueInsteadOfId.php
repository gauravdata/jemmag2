<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request\Processor;

use Aheadworks\Layerednav\App\Request\Parser\ValueInsteadOfId as Parser;
use Aheadworks\Layerednav\App\Request\ProcessorInterface;
use Aheadworks\Layerednav\Model\Config\Source\SeoFriendlyUrl;
use Aheadworks\Layerednav\Model\Url\ConverterPool;
use Magento\Framework\App\RequestInterface;

/**
 * Class ValueInsteadOfId
 * @package Aheadworks\Layerednav\App\Request\Processor
 */
class ValueInsteadOfId implements ProcessorInterface
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var ConverterPool
     */
    private $converterPool;

    /**
     * @param Parser $parser
     * @param ConverterPool $converterPool
     */
    public function __construct(
        Parser $parser,
        ConverterPool $converterPool
    ) {
        $this->parser = $parser;
        $this->converterPool = $converterPool;
    }

    /**
     * {@inheritdoc}
     */
    public function process(RequestInterface $request)
    {
        $parts = $this->parser->parse($request);
        $converter = $this->converterPool
            ->getConverter(
                SeoFriendlyUrl::ATTRIBUTE_VALUE_INSTEAD_OF_ID,
                SeoFriendlyUrl::DEFAULT_OPTION
            );
        $request->setParams(
            array_merge(
                $request->getParams(),
                $converter->convertFilterParams($parts['filterParams'])
            )
        );
        return $request;
    }
}
