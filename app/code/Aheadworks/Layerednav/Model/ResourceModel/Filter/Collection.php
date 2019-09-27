<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Filter;
use Aheadworks\Layerednav\Model\ResourceModel\Filter as FilterResource;
use Aheadworks\Layerednav\Model\ResourceModel\AbstractCollection;
use Aheadworks\Layerednav\Model\Image\Repository as ImageRepository;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Collection
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter
 * @codeCoverageIgnore
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'id';

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param ImageRepository $imageRepository
     * @param AdapterInterface $connection
     * @param AbstractDb $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        ImageRepository $imageRepository,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->imageRepository = $imageRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Filter::class, FilterResource::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            'aw_layerednav_filter_title',
            'id',
            'filter_id',
            ['store_id', 'value'],
            FilterInterface::STOREFRONT_TITLES
        );
        $this->attachRelationTable(
            'aw_layerednav_filter_display_state',
            'id',
            'filter_id',
            ['store_id', 'value'],
            FilterInterface::DISPLAY_STATES
        );
        $this->attachRelationTable(
            'aw_layerednav_filter_sort_order',
            'id',
            'filter_id',
            ['store_id', 'value'],
            FilterInterface::SORT_ORDERS
        );
        $this->attachRelationTable(
            'aw_layerednav_filter_exclude_category',
            'id',
            'filter_id',
            'category_id',
            FilterInterface::EXCLUDE_CATEGORY_IDS
        );

        $this->addImageData();

        /** @var \Magento\Framework\DataObject $item */
        foreach ($this as $item) {
            $title = $this->getStorefrontValue($item->getData(FilterInterface::STOREFRONT_TITLES), true);
            $item->setData(
                FilterInterface::STOREFRONT_TITLE,
                $title ? $title : $item->getData(FilterInterface::DEFAULT_TITLE)
            );
            $item->setData(
                FilterInterface::STOREFRONT_DISPLAY_STATE,
                $this->getStorefrontValue($item->getData(FilterInterface::DISPLAY_STATES), true)
            );
            $item->setData(
                FilterInterface::STOREFRONT_SORT_ORDER,
                $this->getStorefrontValue($item->getData(FilterInterface::SORT_ORDERS), true)
            );
        }

        return parent::_afterLoad();
    }

    /**
     * Add filter by code
     *
     * @param string $code
     * @return $this
     */
    public function addFilterByCode($code = '')
    {
        if ($code) {
            $this->addFieldToFilter(FilterInterface::CODE, ['eq' => $code]);
        }
        return $this;
    }

    /**
     * Add filter by type
     *
     * @param string $type
     * @return $this
     */
    public function addFilterByType($type = '')
    {
        if ($type) {
            $this->addFieldToFilter(FilterInterface::TYPE, ['eq' => $type]);
        }
        return $this;
    }

    /**
     * Add image data to the collection items
     *
     * @return $this
     */
    private function addImageData()
    {
        try {
            $this->attachRelationTable(
                FilterResource::FILTER_IMAGE_TABLE_NAME,
                FilterInterface::ID,
                'filter_id',
                [
                    'image_id'
                ],
                FilterInterface::IMAGE
            );
            /** @var \Magento\Framework\DataObject $item */
            foreach ($this as $item) {
                $attachedData = $item->getData(FilterInterface::IMAGE);
                $firstImageId = reset($attachedData);
                if ($firstImageId) {
                    $firstImage = $this->imageRepository->get($firstImageId);
                    $item->setData(FilterInterface::IMAGE, $firstImage);
                }
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }

        return $this;
    }
}
