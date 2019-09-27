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



namespace Mirasvit\Rma\Controller\Rma;

use Magento\Framework\Controller\ResultFactory;

class OrderTemplate extends \Mirasvit\Rma\Controller\Rma
{
    public function __construct(
        \Mirasvit\Rma\Helper\Controller\Rma\GuestStrategy $guestStrategy,
        \Mirasvit\Rma\Api\Service\Attachment\AttachmentManagementInterface $attachmentManagement,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagementInterface $rmaManagement,
        \Magento\Framework\Registry $registry,
        \Mirasvit\Rma\Helper\Controller\Rma\StrategyFactory $strategyFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->guestStrategy        = $guestStrategy;
        $this->attachmentManagement = $attachmentManagement;
        $this->rmaManagement        = $rmaManagement;
        $this->registry             = $registry;

        parent::__construct($strategyFactory, $customerSession, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function isRequireCustomerAutorization()
    {
        return $this->strategy->isRequireCustomerAutorization();
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $resultPage->getLayout();

        $error = '';
        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            $blockHtml = $layout->getBlock('rma.order.block')->toHtml();
        } else {
            $error = __('Please select order');
        }

        /** @var \Magento\Framework\Controller\Result\Json $response */
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $response->setData([
            'error' => $error,
            'blockHtml' => $blockHtml,
        ]);

        return $response;
    }
}
