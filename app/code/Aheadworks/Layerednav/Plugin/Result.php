<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Plugin;

use Aheadworks\Layerednav\Model\PageConfig;
use Aheadworks\Layerednav\Model\PageTypeResolver;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Result\Page;

/**
 * Class Result
 * @package Aheadworks\Layerednav\Model\Plugin
 */
class Result
{
    const PROCESS_OUTPUT_FLAG = 'aw_layered_nav_process_output';

    /**
     * @var RequestInterface|\Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var PageTypeResolver
     */
    private $pageTypeResolver;

    /**
     * @var PageConfig
     */
    private $pageConfig;

    /**
     * @param RequestInterface $request
     * @param LayoutInterface $layout
     * @param PageTypeResolver $pageTypeResolver
     * @param PageConfig $pageConfig
     */
    public function __construct(
        RequestInterface $request,
        LayoutInterface $layout,
        PageTypeResolver $pageTypeResolver,
        PageConfig $pageConfig
    ) {
        $this->request = $request;
        $this->layout = $layout;
        $this->pageTypeResolver = $pageTypeResolver;
        $this->pageConfig = $pageConfig;
    }

    /**
     * @param ResultInterface $result
     * @param \Closure $proceed
     * @param ResponseInterface $response
     * @return ResultInterface
     */
    public function aroundRenderResult(
        ResultInterface $result,
        \Closure $proceed,
        ResponseInterface $response
    ) {
        if ($this->request->isAjax() && $this->request->getParam(self::PROCESS_OUTPUT_FLAG)) {
            $navigationBlockName = $this->pageTypeResolver->isSearchPage()
                ? 'catalogsearch.leftnav'
                : 'catalog.leftnav';

            /** @var \Magento\Framework\App\Response\Http $response */
            $response->setBody(
                \Zend_Json::encode(
                    [
                        'mainColumn' => $this->layout->renderElement('main'),
                        'navigation' => $this->layout->renderElement($navigationBlockName)
                    ]
                )
            );
            return $result;
        }
        if ($result instanceof Page) {
            $this->pageConfig->apply($result);
        }
        return $proceed($response);
    }
}
