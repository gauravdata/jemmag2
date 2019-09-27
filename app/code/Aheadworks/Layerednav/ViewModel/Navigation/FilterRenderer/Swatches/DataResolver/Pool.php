<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches\DataResolver;

use Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches\DataResolverInterface;

/**
 * Class Pool
 *
 * @package Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches\DataResolver
 */
class Pool
{
    /**
     * @var DataResolverInterface[]
     */
    private $resolvers;

    /**
     * @param array $resolvers
     */
    public function __construct(
        $resolvers = []
    ) {
        $this->resolvers = $resolvers;
    }

    /**
     * Retrieve resolver instance by swathes type
     *
     * @param int $swatchesType
     * @return DataResolverInterface|null
     */
    public function getResolverBySwatchesType($swatchesType)
    {
        if (isset($this->resolvers[$swatchesType])
            && ($this->resolvers[$swatchesType] instanceof DataResolverInterface)
        ) {
            return $this->resolvers[$swatchesType];
        }

        return null;
    }
}
