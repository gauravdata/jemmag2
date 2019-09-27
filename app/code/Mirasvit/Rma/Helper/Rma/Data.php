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



namespace Mirasvit\Rma\Helper\Rma;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $orderRepository;
    private $storeFactory;
    private $storeManager;
    private $rmaManagement;
    private $rmaSearchManagement;
    private $numberConfig;
    private $rmaCollectionFactory;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagementInterface $rmaManagement,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagement\SearchInterface $rmaSearchManagement,
        \Mirasvit\Rma\Service\Config\RmaNumberConfig $numberConfig,
        \Mirasvit\Rma\Model\ResourceModel\Rma\CollectionFactory $rmaCollectionFactory,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->orderRepository      = $orderRepository;
        $this->storeFactory         = $storeFactory;
        $this->storeManager         = $storeManager;
        $this->rmaManagement        = $rmaManagement;
        $this->rmaSearchManagement  = $rmaSearchManagement;
        $this->numberConfig         = $numberConfig;
        $this->rmaCollectionFactory = $rmaCollectionFactory;

        parent::__construct($context);
    }

    /**
     * @param \Mirasvit\Rma\Api\Data\RmaInterface $rma
     * @return string
     */
    public function generateIncrementId(\Mirasvit\Rma\Api\Data\RmaInterface $rma)
    {
        $id = $rma->getId();
        $storeId = (string)$rma->getStoreId();

        $format = $this->numberConfig->getFormat();
        $maxLen = $this->numberConfig->getCounterLength();
        if ($this->numberConfig->isResetCounter($storeId)) {
            $rmas = $this->countOrderRmas($rma);
            $counter = $rmas ?: 1;
        } else {
            $counter = $this->numberConfig->getCounterStart() + $id * $this->numberConfig->getCounterStep() - 1;
        }

        if ($maxLen > strlen($counter)) {
            $counter = str_repeat('0', $maxLen - strlen($counter)) . $counter;
        }

        $result = str_replace('[counter]', $counter, $format);
        $result = str_replace('[store]', $storeId, $result);
        if ($order = $this->rmaManagement->getOrder($rma)) {
            $orderId = $order->getId();
        } elseif ($rma->getData('order_ids')) {
            $orderId = current($rma->getData('order_ids'));
        } else {
            $orderId = $rma->getData('order_id');
        }
        if ($rma->getIsOffline()) {
            $result = str_replace('[order]', $orderId, $result);
        } else {
            $order = $this->orderRepository->get($orderId);
            $result = str_replace('[order]', $order->getIncrementId(), $result);
        }

        $collection = $this->rmaCollectionFactory->create()
            ->addFieldToFilter('main_table.increment_id', ['like' => $result . '%']);
        $collection->getSelect()->order('main_table.increment_id ASC');

        if ($collection->count()) {
            $item = $collection->getLastItem();
            $increment = (int)trim(str_replace($result, '', $item->getIncrementId()), '-');
            $result .= '-' . ($increment + 1);
        }

        return $result;
    }

    /**
     * @return \Mirasvit\Rma\Api\Service\Rma\RmaManagement\SearchInterface
     */
    public function getRmaSearchManagement()
    {
        return $this->rmaSearchManagement;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStoreByOrder($order)
    {
        return ($order) ? $this->storeFactory->create()->load($order->getStoreId()) : $this->storeManager->getStore();
    }

    /**
     * @param \Mirasvit\Rma\Api\Data\RmaInterface $rma
     * @return int
     */
    protected function countOrderRmas(\Mirasvit\Rma\Api\Data\RmaInterface $rma)
    {
        return $this->rmaCollectionFactory->create()->addOrderFilter($rma->getOrderId())->count();
    }
}