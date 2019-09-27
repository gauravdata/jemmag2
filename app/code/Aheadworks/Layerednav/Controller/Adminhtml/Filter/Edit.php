<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Controller\Adminhtml\Filter;

use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Edit
 * @package Aheadworks\Layerednav\Controller\Adminhtml\Filter
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Layerednav::filters';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var FilterRepositoryInterface
     */
    private $filterRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param FilterRepositoryInterface $filterRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        FilterRepositoryInterface $filterRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->filterRepository = $filterRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $filterId = (int)$this->getRequest()->getParam('id');
        if ($filterId) {
            try {
                $this->filterRepository->get($filterId);

                /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
                $resultPage = $this->resultPageFactory->create();
                $resultPage->setActiveMenu('Aheadworks_Layerednav::filters');
                $resultPage->getConfig()->getTitle()->prepend(__('Edit Filter'));

                return $resultPage;
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while editing the filter.')
                );
            }
        } else {
            $this->messageManager->addErrorMessage(
                __('Filter id is not specified.')
            );
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/index');
        return $resultRedirect;
    }
}
