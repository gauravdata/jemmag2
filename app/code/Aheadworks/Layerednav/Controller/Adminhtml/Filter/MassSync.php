<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Controller\Adminhtml\Filter;

use Aheadworks\Layerednav\Api\FilterManagementInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\CollectionFactory as FilterCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassSync
 * @package Aheadworks\Layerednav\Controller\Adminhtml\Filter
 */
class MassSync extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Layerednav::filters';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var FilterCollectionFactory
     */
    private $filterCollectionFactory;

    /**
     * @var FilterManagementInterface
     */
    private $filterManagement;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param FilterCollectionFactory $filterCollectionFactory
     * @param FilterManagementInterface $filterManagement
     */
    public function __construct(
        Context $context,
        Filter $filter,
        FilterCollectionFactory $filterCollectionFactory,
        FilterManagementInterface $filterManagement
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->filterCollectionFactory = $filterCollectionFactory;
        $this->filterManagement = $filterManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            if ($this->isAllItemsSelected()) {
                $this->filterManagement->synchronizeCustomFilters();
                $this->filterManagement->synchronizeAttributeFilters();
                $this->messageManager->addSuccessMessage(
                    __('All filters have been updated.')
                );
            } else {
                $collection = $this->filter->getCollection($this->filterCollectionFactory->create());
                $count = 0;
                foreach ($collection->getAllIds() as $filterId) {
                    $result = $this->filterManagement->synchronizeFilterById($filterId);
                    if ($result) {
                        $count++;
                    }
                }
                if ($count > 0) {
                    $this->messageManager->addSuccessMessage(
                        __('A total of %1 filter(s) have been updated.', $count)
                    );
                } else {
                    $this->messageManager->addErrorMessage(
                        __('None of selected filter(s) can be updated.')
                    );
                }
            }
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while updating the filter(s).')
            );
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Checks if all items in grid are selected
     *
     * @return bool
     */
    private function isAllItemsSelected()
    {
        $excluded = $this->getRequest()->getParam(Filter::EXCLUDED_PARAM);
        return ('false' === $excluded);
    }
}
