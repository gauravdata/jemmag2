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


namespace Mirasvit\Rma\Service\Rma\RmaManagement;

/**
 * Save RMA
 */
class Save implements \Mirasvit\Rma\Api\Service\Rma\RmaManagement\SaveInterface
{
    private $messageRepository;
    private $rmaSearchManagement;
    private $itemUpdateService;
    private $messageAddService;
    private $rmaManagement;
    private $rmaOrderService;
    private $registry;
    private $rmaFactory;
    private $request;
    private $eventManager;
    private $orderAbstractFactory;

    public function __construct(
        \Mirasvit\Rma\Api\Repository\MessageRepositoryInterface $messageRepository,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagement\SearchInterface $rmaSearchManagement,
        \Mirasvit\Rma\Api\Service\Message\MessageManagement\AddInterface $messageAddService,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagementInterface $rmaManagement,
        \Mirasvit\Rma\Api\Service\Rma\RmaOrderInterface $rmaOrderService,
        \Mirasvit\Rma\Model\RmaFactory $rmaFactory,
        \Mirasvit\Rma\Service\Item\Update $itemUpdateService,
        \Mirasvit\Rma\Service\Order\OrderAbstractFactory $orderAbstractFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Registry $registry
    ) {
        $this->messageRepository      = $messageRepository;
        $this->rmaSearchManagement    = $rmaSearchManagement;
        $this->itemUpdateService      = $itemUpdateService;
        $this->messageAddService      = $messageAddService;
        $this->rmaManagement          = $rmaManagement;
        $this->rmaOrderService        = $rmaOrderService;
        $this->registry               = $registry;
        $this->rmaFactory             = $rmaFactory;
        $this->request                = $request;
        $this->eventManager           = $eventManager;
        $this->orderAbstractFactory   = $orderAbstractFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function saveRma($performer, $data, $items)
    {
        $rma = $this->rmaFactory->create();
        if (isset($data['rma_id']) && $data['rma_id']) {
            $rma->load($data['rma_id']);
        }
        unset($data['rma_id']);

        $rma = $this->updateRma($performer, $rma, $data);

        $this->itemUpdateService->updateItems($rma, $items);

        $this->eventManager->dispatch('rma_update_rma_after', ['rma' => $rma, 'performer' => $performer]);

        $filesData = $this->request->getFiles();
        $files = $filesData->toArray();
        if (
            (isset($data['reply']) && $data['reply'] != '') ||
            !empty($files['attachment'][0]['name'])
        ) {
            $this->messageAddService->addMessage($performer, $rma, $data['reply'], $data);
        }

        return $rma;
    }

    /**
     * @param \Mirasvit\Rma\Api\Service\Performer\PerformerInterface $performer
     * @param \Mirasvit\Rma\Api\Data\RmaInterface                    $rma
     * @param array                                                  $data
     *
     * @return \Mirasvit\Rma\Api\Data\RmaInterface
     */
    protected function updateRma($performer, $rma, $data)
    {
        if (isset($data['street2']) && $data['street2'] != '') {
            $data['street'] .= "\n".$data['street2'];
            unset($data['street2']);
        }

        $order = $this->orderAbstractFactory->get($data);
        if (!empty($data['is_offline'])) {
            $orderInfo = current($data['orders']);
            $order->getResource()->load($order, $orderInfo['order_id']);
        } else {
            $orderId = isset($data['order_ids']) ? current($data['order_ids']) : null;
            $order->getResource()->load($order, $orderId);
        }
        if (!$order->getId()) {
            $order = $this->rmaOrderService->getOrder($rma);
        }

        $rma->addData($data);
        $rma->setIfOffline($order->getIsOffline());

        $storeId = $order->getStoreId();
        if (!$storeId && isset($data['store_id'])) {
            $storeId = (int)$data['store_id'];
        }
        $customerId = $order->getCustomerId();
        if (!$customerId && isset($data['customerId'])) {
            $customerId = (int)$data['customerId'];
        }
        $rma->setCustomerId($customerId);
        $rma->setStoreId($storeId);

        if (!$order->getCustomerId() && empty($rma->getEmail())) {
            $this->setRmaCustomerInfo($rma, $performer);
        } else {
            $this->setRmaAddress($rma, $order);
        }

        $performer->setRmaAttributesBeforeSave($rma);

        $rma->save();

        $this->registry->register('current_rma', $rma);

        return $rma;
    }

    /**
     * @param \Mirasvit\Rma\Api\Data\RmaInterface $rma
     * @return void
     */
    protected function setRmaAddress($rma)
    {
        $customer = $this->rmaManagement->getCustomer($rma);
        $address = $customer->getDefaultBillingAddress();
        if ($address) {
            $this->setRmaAddressData($rma, $address);
        } else {
            $this->setRmaCustomerInfo($rma, $customer);
        }
    }

    /**
     * @param \Mirasvit\Rma\Api\Data\RmaInterface $rma
     * @param \Magento\Customer\Model\Address     $address
     * @return void
     */
    public function setRmaAddressData($rma, $address)
    {
        $rma
            ->setFirstname($address->getFirstname())
            ->setLastname($address->getLastname())
            ->setCompany($address->getCompany())
            ->setTelephone($address->getTelephone())
            ->setStreet(implode("\n", $address->getStreet()))
            ->setCity($address->getCity())
            ->setCountryId($address->getCountryId())
            ->setRegionId($address->getRegionId())
            ->setRegion($address->getRegion())
            ->setPostcode($address->getPostcode());
    }

    /**
     * @param \Mirasvit\Rma\Api\Data\RmaInterface $rma
     * @param \Magento\Customer\Model\Customer    $customer
     * @return void
     */
    public function setRmaCustomerInfo($rma, $customer)
    {
        if (!method_exists($customer, 'getEmail') || !$customer->getEmail()) {
            return;
        }
        $rma
            ->setFirstname($customer->getFirstname())
            ->setLastname($customer->getLastname())
            ->setEmail($customer->getEmail())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function markAsReadForCustomer(\Mirasvit\Rma\Api\Data\RmaInterface $rma)
    {
        /** @var \Mirasvit\Rma\Api\Data\MessageInterface $message */
        foreach ($this->rmaSearchManagement->getCustomerUnread($rma) as $message) {
            $message->setIsRead(true);
            $this->messageRepository->save($message);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function markAsReadForUser(\Mirasvit\Rma\Api\Data\RmaInterface $rma)
    {
        /** @var \Mirasvit\Rma\Api\Data\MessageInterface $message */
        foreach ($this->rmaSearchManagement->getUserUnread($rma) as $message) {
            $message->setIsRead(true);
            $this->messageRepository->save($message);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function markAsUnreadForCustomer(\Mirasvit\Rma\Api\Data\RmaInterface $rma)
    {
        /** @var \Mirasvit\Rma\Api\Data\MessageInterface $message */
        foreach ($this->rmaSearchManagement->getCustomerRead($rma) as $message) {
            $message->setIsRead(false);
            $this->messageRepository->save($message);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function markAsUnreadForUser(\Mirasvit\Rma\Api\Data\RmaInterface $rma)
    {
        /** @var \Mirasvit\Rma\Api\Data\MessageInterface $message */
        foreach ($this->rmaSearchManagement->getUserRead($rma) as $message) {
            $message->setIsRead(false);
            $this->messageRepository->save($message);
        }
    }
}