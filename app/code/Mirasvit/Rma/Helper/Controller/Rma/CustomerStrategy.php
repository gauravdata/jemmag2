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



namespace Mirasvit\Rma\Helper\Controller\Rma;

class CustomerStrategy extends AbstractStrategy
{
    /**
     * @var \Magento\Sales\Model\Order[]|null
     */
    private $orders = null;

    private $rmaUrl;
    private $orderManagement;
    private $rmaRepository;
    private $strategySearch;
    private $performerFactory;
    private $orderRepository;
    private $customerSession;

    public function __construct(
        \Mirasvit\Rma\Helper\Rma\Url $rmaUrl,
        \Mirasvit\Rma\Api\Service\Order\OrderManagementInterface $orderManagement,
        \Mirasvit\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Mirasvit\Rma\Api\Service\Strategy\SearchInterface $strategySearch,
        \Mirasvit\Rma\Api\Service\Performer\PerformerFactoryInterface $performerFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->rmaUrl           = $rmaUrl;
        $this->orderManagement  = $orderManagement;
        $this->rmaRepository    = $rmaRepository;
        $this->strategySearch   = $strategySearch;
        $this->performerFactory = $performerFactory;
        $this->orderRepository  = $orderRepository;
        $this->customerSession  = $customerSession;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequireCustomerAutorization()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function initRma(\Magento\Framework\App\RequestInterface $request)
    {
        $id = $request->getParam('id');

        return $this->rmaRepository->getByGuestId($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getRmaId(\Mirasvit\Rma\Api\Data\RmaInterface $rma)
    {
        return $rma->getGuestId();
    }

    /**
     * {@inheritdoc}
     */
    public function getRmaList($order = null)
    {
        return $this->strategySearch->getRmaList(
            $this->customerSession->getCustomerId(),
            $order
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPerformer()
    {
        return $this->performerFactory->create(
            \Mirasvit\Rma\Api\Service\Performer\PerformerFactoryInterface::CUSTOMER,
            $this->getCustomer()
        );
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->customerSession->getCustomer();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedOrderList()
    {
        if ($this->orders === null) {
            $this->orders = $this->orderManagement->getAllowedOrderList($this->getCustomer());
        }

        return $this->orders;
    }

    /**
     * {@inheritdoc}
     */
    public function getRmaUrl(\Mirasvit\Rma\Api\Data\RmaInterface $rma)
    {
        return $this->rmaUrl->getGuestUrl($rma);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewRmaUrl()
    {
        return $this->rmaUrl->getCreateUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($rma)
    {
        return $this->rmaUrl->getGuestUrl($rma);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrintUrl(\Mirasvit\Rma\Api\Data\RmaInterface $rma)
    {
        return $this->rmaUrl->getGuestPrintUrl($rma);
    }
}