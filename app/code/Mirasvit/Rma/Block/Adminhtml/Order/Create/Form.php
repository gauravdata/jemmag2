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


namespace Mirasvit\Rma\Block\Adminhtml\Order\Create;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Form extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);

        $this->request = $context->getRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        if ($rmaId = $this->request->getParam('rma_id')) {
            $this->_sessionQuote->setRmaId($rmaId);
        }

        return parent::_prepareLayout();
    }
}
