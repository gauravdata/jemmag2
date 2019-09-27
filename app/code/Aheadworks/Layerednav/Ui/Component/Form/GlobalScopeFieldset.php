<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui\Component\Form;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Form\Fieldset as FormFieldset;
use Aheadworks\Layerednav\Ui\Context\Checker as ContextChecker;

/**
 * Class GlobalScopeFieldset
 * @package Aheadworks\Layerednav\Ui\Component\Form
 */
class GlobalScopeFieldset extends FormFieldset
{
    /**
     * @var ContextChecker
     */
    protected $contextChecker;

    /**
     * @param ContextInterface $context
     * @param ContextChecker $contextChecker
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        ContextChecker $contextChecker,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $components,
            $data
        );
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
