<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Controller\Adminhtml\Filter;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\CollectionFactory as FilterCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassChangeStatus
 * @package Aheadworks\Layerednav\Controller\Adminhtml\Filter
 */
class MassChangeStatus extends \Magento\Backend\App\Action
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
     * @var FilterRepositoryInterface
     */
    private $filterRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param FilterCollectionFactory $filterCollectionFactory
     * @param FilterRepositoryInterface $filterRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        FilterCollectionFactory $filterCollectionFactory,
        FilterRepositoryInterface $filterRepository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->filterCollectionFactory = $filterCollectionFactory;
        $this->filterRepository = $filterRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $status = $this->getRequest()->getParam('status');
        if (null != $status) {
            try {
                $collection = $this->filter->getCollection($this->filterCollectionFactory->create());

                $completed = 0;
                $failed = 0;
                foreach ($collection->getAllIds() as $filterId) {
                    try {
                        /** @var FilterInterface $filter */
                        $filter = $this->filterRepository->get($filterId);

                        if (in_array($filter->getType(), FilterInterface::CUSTOM_FILTER_TYPES)
                            && $filter->getType() !=FilterInterface::CATEGORY_FILTER
                        ) {
                            continue;
                        }

                        $filter->setIsFilterable($status);
                        $this->filterRepository->save($filter);
                        $completed++;
                    } catch (NoSuchEntityException $e) {
                        $failed++;
                    }
                }

                if ($completed > 0) {
                    $this->messageManager->addSuccessMessage(
                        __('A total of %1 filter(s) have been updated.', $completed)
                    );
                } else {
                    $this->messageManager->addSuccessMessage(
                        __('None of selected filter(s) can be updated.')
                    );
                }
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while updating the filter(s).')
                );
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
