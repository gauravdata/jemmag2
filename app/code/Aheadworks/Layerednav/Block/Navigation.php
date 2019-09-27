<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Block;

use Aheadworks\Layerednav\Model\Layer\Applier;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Layerednav\Block\Filter\Renderer as FilterRenderer;
use Magento\Framework\View\Model\Layout\Merge;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Navigation
 * @package Aheadworks\Layerednav\Block
 */
class Navigation extends Template
{
    /**
     * @var LayerResolver
     */
    private $layerResolver;

    /**
     * @var Applier
     */
    private $applier;

    /**
     * @param Context $context
     * @param LayerResolver $layerResolver
     * @param Applier $applier
     * @param array $data
     */
    public function __construct(
        Context $context,
        LayerResolver $layerResolver,
        Applier $applier,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->layerResolver = $layerResolver;
        $this->applier = $applier;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $this->applier->applyFilters($this->layerResolver->get());
        return $this;
    }

    /**
     * Retrieve filter renderer block
     *
     * @return FilterRenderer|null
     */
    public function getFilterRendererBlock()
    {
        $block = $this->getChildBlock('renderer');
        if ($block && $block instanceof FilterRenderer) {
            return $block;
        } else {
            return null;
        }
    }

    /**
     * Retrieve current page layout
     *
     * @return string|null
     */
    public function getPageLayout()
    {
        $pageLayout = $this->pageConfig->getPageLayout();
        if (empty($pageLayout)) {
            try {
                /** @var Merge $layoutUpdate */
                $layoutUpdate = $this->getLayout()->getUpdate();
                $pageLayout = $layoutUpdate->getPageLayout();
            } catch (LocalizedException $exception) {
                $pageLayout = '';
            }
        }
        return $pageLayout;
    }
}
