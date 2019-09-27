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


namespace Mirasvit\Rma\Service\Resolution;

use Mirasvit\Rma\Api\Data\RmaInterface;
use Mirasvit\Rma\Api\Data\ResolutionInterface;

/**
 *  We put here only methods directly connected with Resolution properties
 */
class ResolutionManagement implements \Mirasvit\Rma\Api\Service\Resolution\ResolutionManagementInterface
{
    /**
     * @var array
     */
    private $rmaItems = [];

    public function __construct(
        \Mirasvit\Rma\Api\Repository\ResolutionRepositoryInterface $resolutionRepository,
        \Mirasvit\Rma\Api\Service\Item\ItemListBuilderInterface $itemListBuilder,
        \Mirasvit\Rma\Model\ResolutionFactory $resolutionFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->resolutionRepository  = $resolutionRepository;
        $this->resolutionFactory     = $resolutionFactory;
        $this->itemListBuilder       = $itemListBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolutionByCode($code)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('code', $code)
        ;

        return $this->resolutionRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * {@inheritdoc}
     */
    public function isExchangeAllowed(RmaInterface $rma)
    {
        /** @var \Mirasvit\Rma\Model\ResourceModel\Resolution\Collection $resolutions */
        $resolutions = $this->resolutionFactory->create()->getCollection()
            ->addFieldToFilter(ResolutionInterface::KEY_EXCHANGE_ORDER_ALLOWED, 1);

        $allowedIds = $resolutions->getAllIds();

        return $this->hasAllowedItem($rma, $allowedIds);
    }

    /**
     * {@inheritdoc}
     */
    public function isReplacementAllowed(RmaInterface $rma)
    {
        /** @var \Mirasvit\Rma\Model\ResourceModel\Resolution\Collection $resolutions */
        $resolutions = $this->resolutionFactory->create()->getCollection()
            ->addFieldToFilter(ResolutionInterface::KEY_REPLACEMENT_ORDER_ALLOWED, 1);

        $allowedIds = $resolutions->getAllIds();

        return $this->hasAllowedItem($rma, $allowedIds);
    }

    /**
     * {@inheritdoc}
     */
    public function isCreditmemoAllowed(RmaInterface $rma)
    {
        /** @var \Mirasvit\Rma\Model\ResourceModel\Resolution\Collection $resolutions */
        $resolutions = $this->resolutionFactory->create()->getCollection()
            ->addFieldToFilter(ResolutionInterface::KEY_CREDITMEMO_ALLOWED, 1);

        $allowedIds = $resolutions->getAllIds();

        return $this->hasAllowedItem($rma, $allowedIds);
    }

    /**
     * @param RmaInterface $rma
     * @param array        $allowedIds
     * @return \Mirasvit\Rma\Api\Data\ItemInterface[]
     */
    private function hasAllowedItem($rma, $allowedIds)
    {
        $allowCreateCreditmemo = false;
        foreach ($this->getRmaItems($rma) as $item) {
            if (in_array($item->getResolutionId(), $allowedIds)) {
                $allowCreateCreditmemo = true;
                break;
            }
        }

        return $allowCreateCreditmemo;
    }

    /**
     * @param RmaInterface $rma
     * @return \Mirasvit\Rma\Api\Data\ItemInterface[]
     */
    private function getRmaItems($rma)
    {
        if (!isset($this->rmaItems[$rma->getId()])) {
            $this->rmaItems[$rma->getId()] = $this->itemListBuilder->getRmaItems($rma);
        }

        return $this->rmaItems[$rma->getId()];
    }
}

