<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Followupemail2\Model\Source\Email;

use Aheadworks\Followupemail2\Api\Data\EmailInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Email\Model\Template\Config as TemplateConfig;
use Magento\Email\Model\ResourceModel\Template\Collection as TemplateCollection;
use Magento\Email\Model\ResourceModel\Template\CollectionFactory as TemplateCollectionFactory;

/**
 * Class Header
 * @package Aheadworks\Followupemail2\Model\Source\Email
 */
class Header implements OptionSourceInterface
{
    const DEFAULT_EMAIL_HEADER_PATH = 'design_email_header_template';

    /**
     * @var TemplateConfig
     */
    private $templateConfig;

    /**
     * @var TemplateCollectionFactory
     */
    private $templateCollectionFactory;

    /**
     * @var array
     */
    private $options;

    /**
     * @param TemplateConfig $templateConfig
     * @param TemplateCollectionFactory $templateCollectionFactory
     */
    public function __construct(
        TemplateConfig $templateConfig,
        TemplateCollectionFactory $templateCollectionFactory
    ) {
        $this->templateConfig = $templateConfig;
        $this->templateCollectionFactory = $templateCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            /** @var TemplateCollection $collection */
            $collection = $this->templateCollectionFactory->create();
            $collection->load();
            $this->options = $collection->toOptionArray();
            $templateLabel = $this->templateConfig->getTemplateLabel(self::DEFAULT_EMAIL_HEADER_PATH);
            $templateLabel = __('%1 (Default)', $templateLabel);
            array_unshift($this->options, ['value' => self::DEFAULT_EMAIL_HEADER_PATH, 'label' => $templateLabel]);
            array_unshift($this->options, ['value' => EmailInterface::NO_TEMPLATE, 'label' => __('No template')]);
        }
        return $this->options;
    }
}
