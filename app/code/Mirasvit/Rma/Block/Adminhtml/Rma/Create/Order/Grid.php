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



namespace Mirasvit\Rma\Block\Adminhtml\Rma\Create\Order;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\Order\Address\Renderer;
use Mirasvit\Rma\Api\Config\RmaPolicyConfigInterface;
use Mirasvit\Rma\Api\Service\Order\OrderManagementInterface;
use Mirasvit\Rma\Helper\Mage;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    private $orderRepository;
    private $orderManagementService;
    private $addressRenderer;
    private $context;
    private $rmaMage;
    private $policyConfig;
    protected $request;

    public function __construct(
        Renderer $addressRenderer,
        Context $context,
        Data $backendHelper,
        OrderRepository $orderRepository,
        OrderManagementInterface $orderManagementService,
        Mage $rmaMage,
        RmaPolicyConfigInterface $policyConfig,
        array $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->context         = $context;
        $this->request         = $context->getRequest();
        $this->rmaMage         = $rmaMage;
        $this->policyConfig    = $policyConfig;
        $this->orderRepository = $orderRepository;
        $this->orderManagementService = $orderManagementService;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('rma_rma_create_order_grid');
        $this->setDefaultSort('increment_id', 'DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $allowedStatuses = $this->policyConfig->getAllowRmaInOrderStatuses();
        $collection = $this->rmaMage->getOrderCollection();
        $collection->addFieldToFilter('status', ['in' => $allowedStatuses]);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('real_order_id', [
            'header' => __('Order #'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'increment_id',
            'filter_index' => 'main_table.increment_id',
        ]);

        if (!$this->context->getStoreManager()->isSingleStoreMode()) {
            $this->addColumn('store_id', [
                'header' => __('Purchased From (Store)'),
                'index' => 'store_id',
                'type' => 'store',
                'store_view' => true,
                'display_deleted' => true,
            ]);
        }

        $this->addColumn('created_at', [
            'header' => __('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ]);

        $this->addColumn('billing_name', [
            'header' => __('Bill to Name'),
            'index' => 'billing_name',
        ]);

        $this->addColumn('shipping_name', [
            'header' => __('Ship to Name'),
            'index' => 'shipping_name',
        ]);

        $this->addColumn('base_grand_total', [
            'header' => __('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type' => 'currency',
            'currency' => 'base_currency_code',
        ]);

        $this->addColumn('grand_total', [
            'header' => __('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type' => 'currency',
            'currency' => 'order_currency_code',
        ]);

        $this->addColumn('entity_id', [
            'header' => __('RMA Allows'),
            'index' => 'entity_id',
            'sortable' => false,
            'renderer' => '\Mirasvit\Rma\Block\Adminhtml\Rma\Create\Order\Column\AllowColumn'
        ]);

        $this->addColumn('other_rmas', [
            'header' => __('RMAs'),
            'index' => 'entity_id',
            'sortable' => false,
            'renderer' => '\Mirasvit\Rma\Block\Adminhtml\Rma\Create\Order\Column\OtherRmasColumn'
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Prepares mass actions for a grid.
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('selected_orders');
        $this->getMassactionBlock()->setUseSelectAll(true);

        $this->getMassactionBlock()->addItem('selected_orders', array(
            'label' => __('Create'),
            'url' => $this->getUrl('*/*/massSelectOrders'),
        ));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        $order = $this->orderRepository->get($row['entity_id']);
        if ($this->orderManagementService->hasUnreturnedItems($order)) {
            return $this->getUrl(
                '*/*/add',
                [
                    'order_id'  => $row->getId(),
                    'ticket_id' => $this->request->getParam('ticket_id')
                ]
            );
        } else {
            return '#';
        }
    }
}
