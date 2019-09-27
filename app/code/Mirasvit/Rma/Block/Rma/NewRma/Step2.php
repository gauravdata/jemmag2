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



namespace Mirasvit\Rma\Block\Rma\NewRma;

class Step2 extends \Magento\Framework\View\Element\Template
{
    private $context;
    private $customerSession;

    /**
     * @var \Mirasvit\Rma\Helper\Controller\Rma\AbstractStrategy
     */
    protected $strategy;

    public function __construct(
        \Mirasvit\Rma\Helper\Controller\Rma\StrategyFactory $strategyFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->strategy        = $strategyFactory->create();
        $this->customerSession = $customerSession;

        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        $order = false;
        if ($orderId = $this->context->getRequest()->getParam('order_id')) {
            $items = $this->strategy->getAllowedOrderList();
            if (isset($items[$orderId])) {
                $order = $items[$orderId];
            }
        }

        return $order;
    }

    /**
     * @return string
     */
    public function getStep2PostUrl()
    {
        return $this->context->getUrlBuilder()->getUrl('returns/rma/save', ['_secure' => true]);
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    protected function getCustomer()
    {
        return $this->customerSession->getCustomer();
    }
}