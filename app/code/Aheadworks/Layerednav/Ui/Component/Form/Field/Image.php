<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui\Component\Form\Field;

use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Ui\Context\Checker as ContextChecker;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Element\DataType\Media;

/**
 * Class Image
 *
 * @package Aheadworks\Layerednav\Ui\Component\Form\Field
 */
class Image extends Media
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ContextChecker
     */
    protected $contextChecker;

    /**
     * @param ContextInterface $context
     * @param Config $config
     * @param ContextChecker $contextChecker
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        Config $config,
        ContextChecker $contextChecker,
        $components = [],
        array $data = []
    ) {
        $this->config = $config;
        $this->contextChecker = $contextChecker;
        parent::__construct($context, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $config = $this->getData('config');
        $config['allowedExtensions'] = $this->config->getAllowedExtensionsForFilterImage();
        if ($this->contextChecker->isNotGlobal($this->getContext())) {
            $config['visible'] = false;
        }
        $this->setData('config', $config);
        parent::prepare();
    }
}
