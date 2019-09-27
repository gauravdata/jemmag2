<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rma
 * @version   2.0.53
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rma\Block\Rma\View;

class Message extends \Magento\Framework\View\Element\Template
{

    private $rmaManagement;
    private $strategy;
    private $rmaMessageUrl;
    private $rmaAttachmentHtml;
    private $attachmentConfig;
    private $registry;
    private $context;

    public function __construct(
        \Mirasvit\Rma\Api\Service\Rma\RmaManagementInterface $rmaManagement,
        \Mirasvit\Rma\Helper\Controller\Rma\StrategyFactory $strategyFactory,
        \Mirasvit\Rma\Helper\Message\Url $rmaMessageUrl,
        \Mirasvit\Rma\Helper\Attachment\Html $rmaAttachmentHtml,
        \Magento\Framework\Registry $registry,
        \Mirasvit\Rma\Api\Config\AttachmentConfigInterface $attachmentConfig,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->rmaManagement     = $rmaManagement;
        $this->strategy          = $strategyFactory->create($context->getRequest());
        $this->rmaMessageUrl     = $rmaMessageUrl;
        $this->rmaAttachmentHtml = $rmaAttachmentHtml;
        $this->attachmentConfig  = $attachmentConfig;
        $this->registry          = $registry;
        $this->context           = $context;

        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface|\Mirasvit\Rma\Model\OfflineOrder|false
     */
    public function getOrder()
    {
        return $this->rmaManagement->getOrder($this->getRma());
    }

    /**
     * @return \Mirasvit\Rma\Api\Data\RmaInterface
     */
    public function getRma()
    {
        return $this->registry->registry('current_rma');
    }

    /**
     * @return string
     */
    public function getMessagePostUrl()
    {
        return $this->rmaMessageUrl->getPostUrl();
    }

    /**
     * @return string
     */
    public function getFileInputHtml()
    {
        return $this->rmaAttachmentHtml->getFileInputHtml($this->getStoreId());
    }

    /**
     * @return int
     */
    public function getAttachmentLimits()
    {
        $limit = '';
        if ($this->attachmentConfig->getFileSizeLimit($this->getStoreId())) {
            $limit = __('Max file size: %1Mb', $this->attachmentConfig->getFileSizeLimit($this->getStoreId()));
        }
        return $limit;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->context->getStoreManager()->getStore()->getId();
    }

    /**
     * @return int
     */
    public function getRmaId()
    {
        $rma = $this->getRma();

        return $this->strategy->getRmaId($rma);
    }

}
