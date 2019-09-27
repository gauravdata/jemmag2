<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App;

use Aheadworks\Layerednav\App\Request\ProcessorPool as RequestProcessorPool;
use Aheadworks\Layerednav\App\Request\MatcherPool as RequestMatcherPool;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\UrlManager;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\Action\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Router\Base as BaseRouter;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Registry;

/**
 * Class Router
 * @package Aheadworks\Layerednav\App
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Router implements RouterInterface
{
    /**
     * Flag indicates that the request has been processed
     */
    const FLAG_PROCESSED = 'aw_layerednav_router_processed';

    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var BaseRouter
     */
    private $baseRouter;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var RequestMatcherPool
     */
    private $requestMatcherPool;

    /**
     * @var RequestProcessorPool
     */
    private $requestProcessorPool;

    /**
     * @var UrlManager
     */
    private $urlManager;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @param ActionFactory $actionFactory
     * @param ResponseInterface $response
     * @param BaseRouter $baseRouter
     * @param Config $config
     * @param RequestProcessorPool $requestProcessorPool
     * @param RequestMatcherPool $requestMatcherPool
     * @param UrlManager $urlManager
     * @param Registry $coreRegistry
     */
    public function __construct(
        ActionFactory $actionFactory,
        ResponseInterface $response,
        BaseRouter $baseRouter,
        Config $config,
        RequestProcessorPool $requestProcessorPool,
        RequestMatcherPool $requestMatcherPool,
        UrlManager $urlManager,
        Registry $coreRegistry
    ) {
        $this->actionFactory = $actionFactory;
        $this->response = $response;
        $this->baseRouter = $baseRouter;
        $this->config = $config;
        $this->requestProcessorPool = $requestProcessorPool;
        $this->requestMatcherPool = $requestMatcherPool;
        $this->urlManager = $urlManager;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function match(RequestInterface $request)
    {
        if (!$this->coreRegistry->registry(self::FLAG_PROCESSED)) {
            $seoUrlFriendly = $this->config->getSeoFriendlyUrlOption();
            $actionInstance = $this->baseRouter->match($request);

            $matched = [];
            foreach ($this->requestMatcherPool->getMatchers() as $matcher) {
                if ($matcher->match($request)) {
                    $matched[] = $matcher->getType();
                }
            }
            if (in_array($seoUrlFriendly, $matched)) {
                $this->requestProcessorPool->getProcessor($seoUrlFriendly)
                    ->process($request);
                $this->coreRegistry->register(self::FLAG_PROCESSED, true);
                return $actionInstance ? : $this->actionFactory->create(Forward::class);
            }
            if (is_array($matched)
                && count($matched)
                && $this->config->isRedirectFromOldUrlsEnabled()
            ) {
                $this->response->setRedirect(
                    $this->urlManager->getCurrentUrl($matched[0], $seoUrlFriendly),
                    '301'
                );
                $request->setDispatched(true);
                return $this->actionFactory->create(Redirect::class);
            }
        }
        return false;
    }
}
