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
use Mirasvit\Rma\Controller\Adminhtml\Rma;

class Edit extends Rma
{
    private $registry;
    private $rmaRepository;
    private $rmaSaveManagement;

    public function __construct(
        \Mirasvit\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagement\SaveInterface $rmaSaveManagement,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->registry          = $registry;
        $this->rmaRepository     = $rmaRepository;
        $this->rmaSaveManagement = $rmaSaveManagement;

        parent::__construct($context);
    }


    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        try {
            $rma = $this->rmaRepository->get($this->getRequest()->getParam('id'));
            $this->registry->register('current_rma', $rma);
            $this->initPage($resultPage)->getConfig()
                ->getTitle()->prepend(__(__('RMA #%1', $rma->getIncrementId())));
            $this->rmaSaveManagement->markAsReadForUser($rma);

            $this->_addContent($resultPage->getLayout()->createBlock('\Mirasvit\Rma\Block\Adminhtml\Rma\Edit'));
            return $resultPage;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->messageManager->addError(__('The rma does not exist.'));
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('*/*/');
        }
    }
}
