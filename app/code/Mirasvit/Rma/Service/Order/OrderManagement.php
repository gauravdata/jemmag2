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


namespace Mirasvit\Rma\Service\Order;

use Magento\Sales\Api\Data\OrderInterface;
use Mirasvit\Rma\Api\Config\OfflineOrderConfigInterface;
use Mirasvit\Rma\Api\Data\RmaInterface;

class OrderManagement implements \Mirasvit\Rma\Api\Service\Order\OrderManagementInterface
{
    private $filterBuilder;
    private $itemListBuilder;
    private $itemQuantityManagement;
    private $offlineOrderConfig;
    private $policyConfig;
    private $offlineOrderRepository;
    private $rmaManagement;
    private $rmaRepository;
    private $historyCollectionFactory;
    private $offlineOrderFactory;
    private $searchCriteriaBuilder;
    private $customerRepository;
    private $orderRepository;

    public function __construct(
        \Mirasvit\Rma\Api\Config\OfflineOrderConfigInterface $offlineOrderConfig,
        \Mirasvit\Rma\Api\Config\RmaPolicyConfigInterface $policyConfig,
        \Mirasvit\Rma\Api\Repository\OfflineOrderRepositoryInterface $offlineOrderRepository,
        \Mirasvit\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Mirasvit\Rma\Api\Service\Item\ItemListBuilderInterface $itemListBuilder,
        \Mirasvit\Rma\Api\Service\Item\ItemManagement\QuantityInterface $itemQuantityManagement,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagementInterface $rmaManagement,
        \Mirasvit\Rma\Model\ResourceModel\OrderStatusHistory\CollectionFactory $historyCollectionFactory,
        \Mirasvit\Rma\Model\OfflineOrderFactory $offlineOrderFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\FilterBuilder $filterBuilder
    ) {
        $this->offlineOrderConfig       = $offlineOrderConfig;
        $this->policyConfig             = $policyConfig;
        $this->offlineOrderRepository   = $offlineOrderRepository;
        $this->rmaRepository            = $rmaRepository;
        $this->itemListBuilder          = $itemListBuilder;
        $this->itemQuantityManagement   = $itemQuantityManagement;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->offlineOrderFactory      = $offlineOrderFactory;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->customerRepository       = $customerRepository;
        $this->orderRepository          = $orderRepository;
        $this->filterBuilder            = $filterBuilder;
        $this->rmaManagement            = $rmaManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedOrderList(\Magento\Customer\Model\Customer $customer)
    {
        $items = $this->getOriginAllowedOrderList($customer);

        if ($this->offlineOrderConfig->isOfflineOrdersEnabled()) {
            $items[OfflineOrderConfigInterface::OFFLINE_ORDER_PLACEHOLDER] = $this->offlineOrderFactory->create()
                ->setId(OfflineOrderConfigInterface::OFFLINE_ORDER_PLACEHOLDER)
                ->setCustomerId($customer->getId())
                ->setCustomerName($customer->getName())
                ->setCustomerEmail($customer->getEmail());
        }

        return $items;
    }

    /**
     * @param \Magento\Customer\Model\Customer|false $customer
     * @return OrderInterface[]
     */
    public function getOriginAllowedOrderList(\Magento\Customer\Model\Customer $customer)
    {
        $returnPeriod    = (int)$this->policyConfig->getReturnPeriod();
        $allowedStatuses = $this->policyConfig->getAllowRmaInOrderStatuses();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('status', $allowedStatuses, 'in')
            ->addFilter('customer_id', (int)$customer->getId())
            //make sure that very old orders, which were created before rma install are not shown
            ->addFilter('updated_at', new \Zend_Db_Expr('SUBDATE(NOW(), '.$returnPeriod.')'), 'gt')
            ;
        $minRmaOrderId = $this->getMinOrderID();
        if ($minRmaOrderId > 0) { // if have logged at least one order
            //show orders which are within allowed return period
            $filters = [];
            $filters[] = $this->filterBuilder
                ->setField('entity_id')
                ->setConditionType('in')
                ->setValue(array_merge([-1], $this->allowedOrderIDs()))
                ->create();

            //show orders, which were created before we started logging
            $filters[] = $this->filterBuilder
                ->setField('entity_id')
                ->setConditionType('lt')
                ->setValue($minRmaOrderId)
                ->create();
            $this->searchCriteriaBuilder->addFilters($filters);
        }
        return $this->orderRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * @param \Magento\Customer\Model\Customer|false $customer
     * @return OrderInterface[]
     */
    public function getOfflineAllowedOrderList(\Magento\Customer\Model\Customer $customer)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('customer_id', (int)$customer->getId())
            ;

        return $this->offlineOrderRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerForOfflineOrder($order)
    {
        return $this->customerRepository->getById((int)$order->getCustomerId());
    }

    /**
     * {@inheritdoc}
     */
    public function getRmaAmount($order)
    {
        return count($this->rmaManagement->getRmasByOrder($order));
    }

    /**
     * {@inheritdoc}
     */
    public function hasUnreturnedItems($order)
    {
        if ($order->getIsOffline()) {
            return true;
        }
        $allow = false;
        $rmas = $this->rmaManagement->getRmasByOrder($order);
        foreach ($rmas as $rma) {
            $items = $this->itemListBuilder->getRmaItems($rma);
            foreach ($items as $item) {
                if (!$item->getIsOffline()) {
                    $qty = $this->itemQuantityManagement->getQtyAvailable($item);
                    if ($qty) {
                        $allow = true;
                        break;
                    }
                }
            }
        }

        return ($allow || !count($rmas));
    }

    /**
     * {@inheritdoc}
     */
    public function isReturnAllowed($order)
    {
        if (is_object($order)) {
            $order = $order->getId();
        }
        $allowedStatuses = $this->policyConfig->getAllowRmaInOrderStatuses();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('status', $allowedStatuses, 'in')
            ->addFilter('entity_id', (int)$order)
        ;

        return (bool)$this->orderRepository->getList($searchCriteria->create())->getTotalCount();
    }

    /**
     * Returns orders, which received allowed return status within return period.
     *
     * @return int[]
     */
    public function allowedOrderIDs()
    {
        $allowedStatuses = $this->policyConfig->getAllowRmaInOrderStatuses();
        $returnPeriod    = (int)$this->policyConfig->getReturnPeriod();
        /** @var \Mirasvit\Rma\Model\ResourceModel\OrderStatusHistory\Collection $collection */
        $collection = $this->historyCollectionFactory->create();
        $collection->removeAllFieldsFromSelect()
            ->addFieldToSelect('order_id')
            ->addFieldToFilter('status', ['in' => $allowedStatuses])
            ->addFieldToFilter(
                new \Zend_Db_Expr('ADDDATE(created_at, '.$returnPeriod.')'),
                ['gt' => new \Zend_Db_Expr('NOW()')]
            )
        ;

        return $collection->getColumnValues('order_id');
    }

    public function getMinOrderID()
    {
        $collection = $this->historyCollectionFactory->create();
        $collection->getSelect()->columns(new \Zend_Db_Expr('MIN(order_id)'));
        if ($item = $collection->getFirstItem()) {
            return $item->getOrderId();
        }
        return 0;
    }
}
