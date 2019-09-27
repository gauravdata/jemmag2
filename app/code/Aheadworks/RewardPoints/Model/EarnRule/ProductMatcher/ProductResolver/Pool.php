<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\ProductResolver;

use Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\ProductResolverInterface;

/**
 * Class Pool
 * @package Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\ProductResolver
 */
class Pool
{
    /**
     * Default resolver code
     */
    const DEFAULT_RESOLVER_CODE = 'default';

    /**
     * @var ProductResolverInterface[]
     */
    private $resolvers;

    /**
     * @param ProductResolverInterface[] $resolvers
     */
    public function __construct(
        $resolvers = []
    ) {
        $this->resolvers = $resolvers;
    }

    /**
     * Get resolvers
     *
     * @return ProductResolverInterface[]
     */
    public function getResolvers()
    {
        return $this->resolvers;
    }

    /**
     * Get resolver by product code
     *
     * @param string $code
     * @return ProductResolverInterface
     * @throws \Exception
     */
    public function getResolverByCode($code)
    {
        if (!isset($this->resolvers[$code])) {
            $code = self::DEFAULT_RESOLVER_CODE;
        }
        $resolver = $this->resolvers[$code];
        if (!$resolver instanceof ProductResolverInterface) {
            throw new \Exception('Product resolver must implements ProductResolverInterface');
        }

        return $resolver;
    }
}
