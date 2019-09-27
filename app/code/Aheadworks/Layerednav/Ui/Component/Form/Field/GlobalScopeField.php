<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui\Component\Form\Field;

use Aheadworks\Layerednav\Ui\Context\Checker as ContextChecker;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Form\Field as FormField;

/**
 * Class GlobalScopeField
 * @package Aheadworks\Layerednav\Ui\Component\Form\Field
 */
class GlobalScopeField extends FormField
{
    /**
     * @var ContextChecker
     */
    protected $contextChecker;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ContextChecker $contextChecker
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ContextChecker $contextChecker,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->contextChecker = $contextChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        if ($this->contextChecker->isNotGlobal($this->getContext())) {
            $config = $this->getConfig();
            $config['visible'] = false;
            $this->setConfig($config);
        }

        parent::prepare();
    }
}
