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



namespace Mirasvit\Rma\Repository;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

class OfflineOrderRepository implements \Mirasvit\Rma\Api\Repository\OfflineOrderRepositoryInterface
{
    use \Mirasvit\Rma\Repository\RepositoryFunction\Create;
    use \Mirasvit\Rma\Repository\RepositoryFunction\GetList;

    /**
     * @var \Mirasvit\Rma\Model\OfflineOrder[]
     */
    protected $instances = [];

    public function __construct(
        \Mirasvit\Rma\Model\OfflineOrderFactory $objectFactory,
        \Mirasvit\Rma\Model\ResourceModel\OfflineOrder $offlineOrderResource,
        \Mirasvit\Rma\Model\ResourceModel\OfflineOrder\CollectionFactory $offlineOrderCollectionFactory,
        \Mirasvit\Rma\Api\Data\OfflineOrderSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->objectFactory                 = $objectFactory;
        $this->offlineOrderResource          = $offlineOrderResource;
        $this->searchResultsFactory          = $searchResultsFactory;
        $this->offlineOrderCollectionFactory = $offlineOrderCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Mirasvit\Rma\Api\Data\OfflineOrderInterface $offlineOrder)
    {
        $this->offlineOrderResource->save($offlineOrder);

        return $offlineOrder;
    }

    /**
     * {@inheritdoc}
     */
    public function get($offlineOrderId)
    {
        if (!isset($this->instances[$offlineOrderId])) {
            /** @var \Mirasvit\Rma\Model\OfflineOrder $offlineOrder */
            $offlineOrder = $this->objectFactory->create();
            $offlineOrder->getResource()->load($offlineOrder, $offlineOrderId);
            if (!$offlineOrder->getId()) {
                throw NoSuchEntityException::singleField('id', $offlineOrderId);
            }
            $this->instances[$offlineOrderId] = $offlineOrder;
        }

        return $this->instances[$offlineOrderId];
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Mirasvit\Rma\Api\Data\OfflineOrderInterface $offlineOrder)
    {
        try {
            $offlineOrderId = $offlineOrder->getId();
            $this->offlineOrderResource->delete($offlineOrder);
        } catch (\Exception $e) {
            throw new StateException(
                __(
                    'Cannot delete item with id %1',
                    $offlineOrder->getId()
                ),
                $e
            );
        }
        unset($this->instances[$offlineOrderId]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($itemId)
    {
        $item = $this->get($itemId);
        return  $this->delete($item);
    }

    /**
     * Validate item process
     *
     * @param  \Mirasvit\Rma\Model\OfflineOrder $item
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function validateItem(\Mirasvit\Rma\Model\OfflineOrder $item)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        /** @var \Mirasvit\Rma\Model\ResourceModel\OfflineOrder\Collection $collection */
        return $this->offlineOrderCollectionFactory->create();
    }
}
