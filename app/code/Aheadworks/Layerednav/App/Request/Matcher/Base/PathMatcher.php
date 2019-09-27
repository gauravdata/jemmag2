<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request\Matcher\Base;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Router\Base as BaseRouter;
use Magento\UrlRewrite\Controller\Router as UrlRewriteRouter;

/**
 * Class PathMatcher
 * @package Aheadworks\Layerednav\App\Request\Matcher\Base
 */
class PathMatcher
{
    /**
     * @var BaseRouter
     */
    private $baseRouter;

    /**
     * @var UrlRewriteRouter
     */
    private $urlRewriteRouter;

    /**
     * @var array
     */
    private $paths = ['catalog/category/view'];

    /**
     * @param BaseRouter $baseRouter
     * @param UrlRewriteRouter $urlRewriteRouter
     * @param array $paths
     */
    public function __construct(
        BaseRouter $baseRouter,
        UrlRewriteRouter $urlRewriteRouter,
        $paths = []
    ) {
        $this->baseRouter = $baseRouter;
        $this->urlRewriteRouter = $urlRewriteRouter;
        $this->paths = array_merge($this->paths, $paths);
    }

    /**
     * Match action by request path
     *
     * @param RequestInterface|Http $request
     * @return bool
     */
    public function match($request)
    {
        $this->urlRewriteRouter->match($request);
        $actionInstance = $this->baseRouter->match($request);
        if ($actionInstance) {
            $path = implode(
                '/',
                [
                    $request->getModuleName(),
                    $request->getControllerName(),
                    $request->getActionName()
                ]
            );
            return in_array($path, $this->paths);
        }
        return false;
    }
}
