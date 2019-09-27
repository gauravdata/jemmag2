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

use Mage;
use Magento\Framework\Controller\ResultFactory;

class MassSelectOrders extends \Mirasvit\Rma\Controller\Adminhtml\Rma
{
    public function __construct(
        \Mirasvit\Rma\Model\RmaFactory $rmaFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->rmaFactory = $rmaFactory;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $ids = $this->getRequest()->getParam('selected_orders');
        if (!is_array($ids)) {
            $this->messageManager->addErrorMessage(__('Please select order(s)'));
        } else {
            $this->_redirect('*/*/add', array('orders_id' => implode(',', $ids)));

            return;
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
