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



namespace Mirasvit\Rma\Block\Rma;

use Mirasvit\Rma\Api\Data\StatusInterface;
use Mirasvit\Rma\Api\Repository\StatusRepositoryInterface;

class View extends \Magento\Framework\View\Element\Template
{
    private   $registry;

    protected $fieldManagement;

    protected $rmaHtmlHelper;

    protected $messageManagement;

    protected $messageSearchManagement;

    protected $rmaManagement;

    protected $rmaOrderHtml;

    private   $statusRepository;

    public function __construct(
        \Mirasvit\Rma\Api\Service\Message\MessageManagementInterface $messageManagement,
        \Mirasvit\Rma\Api\Service\Message\MessageManagement\SearchInterface $messageSearchManagement,
        \Mirasvit\Rma\Helper\Order\Html $rmaOrderHtml,
        \Mirasvit\Rma\Helper\Rma\Html $rmaHtmlHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagementInterface $rmaManagement,
        \Mirasvit\Rma\Api\Service\Field\FieldManagementInterface $fieldManagement,
        StatusRepositoryInterface $statusRepository,
        array $data = []
    ) {
        $this->messageManagement       = $messageManagement;
        $this->messageSearchManagement = $messageSearchManagement;
        $this->rmaOrderHtml            = $rmaOrderHtml;
        $this->rmaHtmlHelper           = $rmaHtmlHelper;
        $this->registry                = $registry;
        $this->rmaManagement           = $rmaManagement;
        $this->fieldManagement         = $fieldManagement;
        $this->statusRepository        = $statusRepository;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($rma = $this->getRma()) {
            $this->pageConfig->getTitle()->set(__('RMA #%1', $rma->getIncrementId()));
            $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
            if ($pageMainTitle) {
                $pageMainTitle->setPageTitle(
                    __('RMA #%1 - %2', $rma->getIncrementId(),
                        $this->rmaManagement->getStatus($rma)->getName())
                );
            }
        }
    }

    /**
     * @return \Mirasvit\Rma\Api\Data\RmaInterface
     */
    public function getRma()
    {
        return $this->registry->registry('current_rma');
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface[]|\Mirasvit\Rma\Model\OfflineOrder[]
     */
    public function getOrders()
    {
        return $this->rmaManagement->getOrders($this->getRma());
    }

    public function getProgress()
    {
        $statuses = $this->statusRepository->getCollection();
        $statuses->addActiveFilter();
        $statuses->getSelect()->order(StatusInterface::KEY_SORT_ORDER . ' asc');

        $progress = [];

        foreach ($statuses as $status) {
            if ($status->getCode() == 'rejected') {
                continue;
            }

            $progress[] = [
                'label'  => $status->getName(),
                'active' => false,
            ];

            if ($status->getId() === $this->getRma()->getStatusId()) {
                foreach (array_keys($progress) as $key) {
                    $progress[$key]['active'] = true;
                }
            }
        }

        return $progress;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface|\Mirasvit\Rma\Model\OfflineOrder $order
     *
     * @return string
     */
    public function getOrderLabel($order)
    {
        if ($order->getIsOffline()) {
            return $order->getReceiptNumber();
        } else {
            return $order->getIncrementId();
        }
    }
}
