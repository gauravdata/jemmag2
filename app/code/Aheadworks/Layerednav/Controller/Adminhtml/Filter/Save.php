<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Controller\Adminhtml\Filter;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterfaceFactory;
use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Aheadworks\Layerednav\Api\Data\FilterCategoryInterfaceFactory;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Aheadworks\Layerednav\Model\Filter;
use Aheadworks\Layerednav\Ui\FilterDataProvider;
use Aheadworks\Layerednav\Model\Filter\PostDataProcessor as FilterPostDataProcessor;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 * @package Aheadworks\Layerednav\Controller\Adminhtml\Filter
 */
class Save extends \Magento\Backend\App\Action
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
     * @var FilterInterfaceFactory
     */
    private $filterFactory;

    /**
     * @var FilterRepositoryInterface
     */
    private $filterRepository;

    /**
     * @var FilterCategoryInterfaceFactory
     */
    private $filterCategoryFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var FilterPostDataProcessor
     */
    private $filterPostDataProcessor;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param FilterInterfaceFactory $filterFactory
     * @param FilterRepositoryInterface $filterRepository
     * @param FilterCategoryInterfaceFactory $filterCategoryFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param FilterPostDataProcessor $filterPostDataProcessor
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        FilterInterfaceFactory $filterFactory,
        FilterRepositoryInterface $filterRepository,
        FilterCategoryInterfaceFactory $filterCategoryFactory,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        FilterPostDataProcessor $filterPostDataProcessor
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->filterFactory = $filterFactory;
        $this->filterRepository = $filterRepository;
        $this->filterCategoryFactory = $filterCategoryFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->filterPostDataProcessor = $filterPostDataProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            try {
                $preparedData = $this->filterPostDataProcessor->process($data);

                /** @var FilterInterface $filter */
                $filter = $this->performSave($preparedData);

                $this->messageManager->addSuccessMessage(__('Filter was successfully saved.'));

                if ($this->getRequest()->getParam('back')) {
                    $params = [
                        'id' => $filter->getId()
                    ];
                    if (isset($data['store_id']) && $data['store_id']) {
                        $params['store'] = $data['store_id'];
                    }
                    return $resultRedirect->setPath('*/*/edit', $params);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the filter data.')
                );
            }
            $id = isset($data['id']) ? $data['id'] : false;
            if ($id) {
                $this->dataPersistor->set(FilterDataProvider::FILTER_PERSISTOR_KEY, $data);
                return $resultRedirect->setPath('*/*/edit', ['id' => $id, '_current' => true]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Perform save
     *
     * @param array $data
     * @return FilterInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function performSave($data)
    {
        $id = isset($data['id']) ? $data['id'] : false;
        $storeId = $data['store_id'];

        /** @var FilterInterface|Filter $filter */
        $filter = $id
            ? $this->filterRepository->get($id)
            : $this->filterFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $filter,
            $data,
            FilterInterface::class
        );
        if (isset($data['category_filter_data'])) {
            /** @var FilterCategoryInterface $filterCategory */
            $filterCategory = $this->filterCategoryFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $filterCategory,
                $data['category_filter_data'],
                FilterCategoryInterface::class
            );
            $filter->setCategoryFilterData($filterCategory);
        }

        return $this->filterRepository->save($filter, $storeId);
    }
}
