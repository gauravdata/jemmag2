<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Followupemail2\Controller\Unsubscribe;

use Aheadworks\Followupemail2\Api\EventManagementInterface;
use Magento\Framework\App\Action\Context;

/**
 * Class All
 * @package Aheadworks\Followupemail2\Controller\Unsubscribe
 */
class All extends \Magento\Framework\App\Action\Action
{
    /**
     * @var EventManagementInterface
     */
    private $eventManagement;

    /**
     * @param Context $context
     * @param EventManagementInterface $eventManagement
     */
    public function __construct(
        Context $context,
        EventManagementInterface $eventManagement
    ) {
        parent::__construct($context);
        $this->eventManagement = $eventManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $code = $this->getRequest()->getParam('code');
        if ($code) {
            if ($this->eventManagement->unsubscribeFromAll($code)) {
                $this->messageManager->addSuccessMessage(__('You have been successfully unsubscribed.'));
            }
        }
        return $resultRedirect->setPath('/');
    }
}
