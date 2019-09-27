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



namespace Mirasvit\Rma\Controller\Adminhtml\Rma;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Mirasvit\Rma\Api\Config\OfflineOrderConfigInterface;
use Mirasvit\Rma\Controller\Adminhtml\Rma;

class Add extends Rma
{
    private $offlineConfig;
    private $rmaRepository;
    private $rmaAdd;
    private $registry;
    private $orderRepository;
    private $orderManagementService;

    public function __construct(
        \Mirasvit\Rma\Api\Config\OfflineOrderConfigInterface $offlineConfig,
        \Mirasvit\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Mirasvit\Rma\Api\Service\Order\OrderManagementInterface $orderManagementService,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagement\AddInterface $rmaAdd,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->offlineConfig = $offlineConfig;
        $this->rmaRepository = $rmaRepository;
        $this->rmaAdd        = $rmaAdd;
        $this->registry      = $registry;

        $this->orderRepository = $orderRepository;
        $this->orderManagementService = $orderManagementService;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('New RMA'));

        $data = $this->backendSession->getFormData(true);
        if ($ticketId = $this->getRequest()->getParam('ticket_id')) {
            $data['ticket_id'] = $ticketId;
        }

        $rma = $this->rmaRepository->create();
        if (!empty($data)) {
            $rma->setData($data);
        }

        $this->registry->register('current_rma', $rma);
        if ($ordersId = $this->getRequest()->getParam('orders_id')) {
            try {
                $firstOrder = null;
                $ordersId = explode(',', $ordersId);
                $unavialableOrders = [];
                foreach ($ordersId as $orderId) {
                    $order = $this->orderRepository->get($orderId);
                    if (!$firstOrder) {
                        $firstOrder = $order;
                    }
                    if (!$this->orderManagementService->hasUnreturnedItems($order)) {
                        $unavialableOrders[] = $order->getIncrementId();
                    }
                }
                if ($unavialableOrders) {
                    $this->messageManager->addNoticeMessage(
                        __('There is impossible to create one more Return for the order #%1',
                            implode(', #', $unavialableOrders))
                    );
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

                    return $resultRedirect->setPath('rma/rma/index');
                }
            } catch (NoSuchEntityException $e) {}
            if ($ordersId[0] == OfflineOrderConfigInterface::OFFLINE_ORDER_PLACEHOLDER) {
                if (!$this->offlineConfig->isOfflineOrdersEnabled()) {
                    $this->_addContent($resultPage->getLayout()->getBlock('rma_adminhtml_rma_create'));
                    return $resultPage;
                }
                $customerId  = (int)$this->getRequest()->getParam('customer_id');
                $this->rmaAdd->initFromOfflineOrder($rma, $customerId);
            } else {
                $this->rmaAdd->initFromOrder($rma, $firstOrder);
                $rma->setOrderIds($ordersId);

                // update stored RMA info
                $this->registry->unregister('current_rma');
                $this->registry->register('current_rma', $rma);
            }
            $this->_addContent($resultPage->getLayout()->createBlock('\Mirasvit\Rma\Block\Adminhtml\Rma\Edit'));
        } else {
            $this->_addContent($resultPage->getLayout()->getBlock('rma_adminhtml_rma_create'));
        }

        return $resultPage;
    }
}
