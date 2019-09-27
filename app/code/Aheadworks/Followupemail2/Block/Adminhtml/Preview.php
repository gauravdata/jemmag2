<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Followupemail2\Block\Adminhtml;

use Aheadworks\Followupemail2\Api\Data\PreviewInterface;
use Aheadworks\Followupemail2\Api\Data\PreviewInterfaceFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;

/**
 * Class Preview
 * @package Aheadworks\Followupemail2\Block\Adminhtml
 * @codeCoverageIgnore
 */
class Preview extends \Magento\Backend\Block\Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_Followupemail2::preview.phtml';

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var PreviewInterfaceFactory
     */
    private $previewFactory;

    /**
     * @var PreviewInterface
     */
    private $preview;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PreviewInterfaceFactory $previewFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PreviewInterfaceFactory $previewFactory,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->previewFactory = $previewFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get sender name
     *
     * @return string
     */
    public function getSenderName()
    {
        /** @var PreviewInterface $preview */
        $preview = $this->getPreview();
        return $preview->getSenderName();
    }

    /**
     * Get sender email
     *
     * @return string
     */
    public function getSenderEmail()
    {
        /** @var PreviewInterface $preview */
        $preview = $this->getPreview();
        return $preview->getSenderEmail();
    }

    /**
     * Get recipient name
     *
     * @return string
     */
    public function getRecipientName()
    {
        /** @var PreviewInterface $preview */
        $preview = $this->getPreview();
        return $preview->getRecipientName();
    }

    /**
     * Get recipient email
     *
     * @return string
     */
    public function getRecipientEmail()
    {
        /** @var PreviewInterface $preview */
        $preview = $this->getPreview();
        return $preview->getRecipientEmail();
    }

    /**
     * Get email body
     *
     * @return string
     */
    public function getMessageContent()
    {
        /** @var PreviewInterface $preview */
        $preview = $this->getPreview();
        return $preview->getContent();
    }

    /**
     * Get email subject
     *
     * @return null|string
     */
    public function getMessageSubject()
    {
        /** @var PreviewInterface $preview */
        $preview = $this->getPreview();
        return $preview->getSubject();
    }

    /**
     * Get preview
     *
     * @return PreviewInterface
     */
    private function getPreview()
    {
        if (!$this->preview) {
            if ($this->coreRegistry->registry('aw_followupemail2_preview')) {
                $this->preview = $this->coreRegistry->registry('aw_followupemail2_preview');
            } else {
                $this->preview = $this->previewFactory->create();
            }
        }
        return $this->preview;
    }

    /**
     * Set preview
     *
     * @param PreviewInterface $preview
     * @return $this
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;

        return $this;
    }

    /**
     * Get current store id
     *
     * @return int
     */
    private function getStoreId()
    {
        /** @var PreviewInterface $preview */
        $preview = $this->getPreview();
        return $preview->getStoreId();
    }
}
