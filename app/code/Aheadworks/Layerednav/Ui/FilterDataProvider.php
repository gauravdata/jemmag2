<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Store\Model\Store;
use Magento\Ui\DataProvider\Modifier\PoolInterface as ModifierPoolInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class FilterDataProvider
 * @package Aheadworks\Layerednav\Ui
 */
class FilterDataProvider extends AbstractDataProvider
{
    /**
     * @var string
     */
    private $requestScopeFieldName;

    /**
     * Layered navigation filter key
     */
    const FILTER_PERSISTOR_KEY = 'aw_layerednav_filter';

    /**
     * @var FilterRepositoryInterface
     */
    private $filterRepository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ModifierPoolInterface
     */
    private $modifierPool;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param string $requestScopeFieldName
     * @param FilterRepositoryInterface $filterRepository
     * @param DataPersistorInterface $dataPersistor
     * @param DataObjectProcessor $dataObjectProcessor
     * @param RequestInterface $request
     * @param ModifierPoolInterface $modifierPool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        $requestScopeFieldName,
        FilterRepositoryInterface $filterRepository,
        DataPersistorInterface $dataPersistor,
        DataObjectProcessor $dataObjectProcessor,
        RequestInterface $request,
        ModifierPoolInterface $modifierPool,
        array $meta = [],
        array $data = []
    ) {
        $this->requestScopeFieldName = $requestScopeFieldName;
        $this->filterRepository = $filterRepository;
        $this->dataPersistor = $dataPersistor;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->request = $request;
        $this->modifierPool = $modifierPool;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];
        $dataFromForm = $this->dataPersistor->get(self::FILTER_PERSISTOR_KEY);
        if (!empty($dataFromForm)) {
            if (isset($dataFromForm['id'])) {
                $data[$dataFromForm['id']] = $dataFromForm;
            } else {
                $data[null] = $dataFromForm;
            }
            $this->dataPersistor->clear(self::FILTER_PERSISTOR_KEY);
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            if ($id) {
                $storeId = $this->request->getParam($this->requestScopeFieldName, Store::DEFAULT_STORE_ID);

                /** @var FilterInterface $filter */
                $filter = $this->filterRepository->get($id, $storeId);

                $filterData = $this->dataObjectProcessor->buildOutputDataArray(
                    $filter,
                    FilterInterface::class
                );
                $filterData['store_id'] = $storeId;
                $data[$filter->getId()] = $this->prepareData($filterData);
            }
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(Filter $filter)
    {
        return $this;
    }

    /**
     * Prepare data
     *
     * @param array $data
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function prepareData($data)
    {
        /** @var ModifierInterface $modifier */
        foreach ($this->modifierPool->getModifiersInstances() as $modifier) {
            $data = $modifier->modifyData($data);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->modifierPool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
