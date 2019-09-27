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

use Magento\Sales\Model\OrderFactory;
use Mirasvit\Rma\Model\OfflineOrderFactory;
use Magento\Sales\Model\Order;
use Mirasvit\Rma\Model\OfflineOrder;

class OrderAbstractFactory
{
    public function __construct(
        OrderFactory $orderFactory,
        OfflineOrderFactory $offlineOrderFactory
    ) {
        $this->orderFactory = $orderFactory;
        $this->offlineOrderFactory = $offlineOrderFactory;
    }

    /**
     * @param array $data
     * @return Order|OfflineOrder
     */
    public function get($data)
    {
        if (isset($data['is_offline']) && $data['is_offline']) {
            $order = $this->getOfflineOrder();
        } else {
            $order = $this->getOrder();
            if (!empty($data['order_id'])) {
                $order->getResource()->load($order, $data['order_id']);
            }
        }

        return $order;
    }

    /**
     * @return \Mirasvit\Rma\Model\OfflineOrder
     */
    public function getOfflineOrder()
    {
        return $this->offlineOrderFactory->create();
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->orderFactory->create();
    }
}