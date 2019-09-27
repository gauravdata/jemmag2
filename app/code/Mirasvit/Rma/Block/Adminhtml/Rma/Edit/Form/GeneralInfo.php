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


namespace Mirasvit\Rma\Block\Adminhtml\Rma\Edit\Form;


class GeneralInfo extends \Magento\Backend\Block\Template
{
    private $customFields;
    private $rmaNumberConfig;
    private $rmaManagement;
    private $rmaOrderService;
    private $rmaUserHtml;
    private $rmaUrl;
    private $rmaOption;
    private $storeHelper;
    private $formFactory;
    private $convertDataObject;
    private $rmaManagementService;

    public function __construct(
        \Mirasvit\Rma\Block\Adminhtml\Rma\Edit\Form\Generalinfo\CustomFields $customFields,
        \Mirasvit\Rma\Api\Config\RmaNumberConfigInterface $rmaNumberConfig,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagementInterface $rmaManagementService,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagementInterface $rmaManagement,
        \Mirasvit\Rma\Api\Service\Rma\RmaOrderInterface $rmaOrderService,
        \Mirasvit\Rma\Helper\User\Html $rmaUserHtml,
        \Mirasvit\Rma\Helper\Rma\Url $rmaUrl,
        \Mirasvit\Rma\Helper\Rma\Option $rmaOption,
        \Mirasvit\Rma\Helper\Store $storeHelper,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Convert\DataObject $convertDataObject,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->customFields           = $customFields;
        $this->rmaNumberConfig        = $rmaNumberConfig;
        $this->rmaManagement          = $rmaManagement;
        $this->rmaOrderService        = $rmaOrderService;
        $this->rmaUserHtml            = $rmaUserHtml;
        $this->rmaUrl                 = $rmaUrl;
        $this->rmaOption              = $rmaOption;
        $this->storeHelper            = $storeHelper;
        $this->formFactory            = $formFactory;
        $this->convertDataObject      = $convertDataObject;
        $this->rmaManagementService   = $rmaManagementService;

        parent::__construct($context, $data);
    }


    /**
     * General information form
     *
     * @param \Mirasvit\Rma\Api\Data\RmaInterface $rma
     *
     * @return string
     */
    public function getGeneralInfoFormHtml(\Mirasvit\Rma\Api\Data\RmaInterface $rma)
    {
        $form = $this->formFactory->create();
        /** @var \Magento\Framework\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $form->addFieldset('edit_fieldset', []);

        if ($rma->getId()) {
            $fieldset->addField('rma_id', 'hidden', [
                'name'  => 'rma_id',
                'value' => $rma->getId(),
            ]);
        }

        if ($this->rmaNumberConfig->isManualNumberAllowed()) {
            $element = $fieldset->addField('increment_id', 'text', [
                'label' => __('RMA #'),
                'name'  => 'increment_id',
                'value' => $rma->getIncrementId(),
            ]);

            if (!$rma->getId()) {
                $element->setNote('will be generated automatically, if empty');
            }
        }

        $orders = $this->rmaManagement->getOrders($rma);
        if (!$orders && !$rma->getId()) {
            $customerId = (int)$this->getRequest()->getParam('customer_id');
            $fieldset->addField('is_offline', 'hidden', [
                'name'   => 'is_offline',
                'value'  => 1,
            ]);
            $fieldset->addField('customer_id', 'hidden', [
                'name'   => 'customer_id',
                'value'  => $customerId,
            ]);
            $fieldset->addField('store_id', 'select', [
                'name'   => 'store_id',
                'value'  => 1,
                'label'  => __('Store View'),
                'values' => $this->storeHelper->getCoreStoreOptionArray(),
            ]);
        } elseif ($orders) {
            $str = '';
            foreach ($orders as $order) {
                $str .= '<div>';
                if ($order->getIsOffline()) {
                    $str .= $this->escapeHtml($order->getReceiptNumber());
                } else {
                    if ($order) {
                        $url  = $this->getUrl('sales/order/view', ['order_id' => $order->getId()]);
                        $str .= '<a href="' . $url . '" target="_blank">#' . $order->getIncrementId() . '</a><br/>';
                    } else {
                        $str .= __('Removed Order');
                    }
                }
                $str .= '</div>';
            }
            $fieldset->addField('order_id', 'note', [
                'name'  => 'order_id',
                'label' => __('Order #'),
                'text' => $str,
            ]);
        }

        if ($rma->getTicketId()) {
            $fieldset->addField('ticket_id', 'hidden', [
                'name'  => 'ticket_id',
                'value' => $rma->getTicketId(),
            ]);
            $ticket = $this->rmaManagement->getTicket($rma);
            $fieldset->addField('ticket_link', 'link', [
                'label'  => __('Created From Ticket'),
                'name'   => 'ticket_link',
                'value'  => '#' . $ticket->getCode(),
                'href'   => $ticket->getBackendUrl(),
                'target' => '_blank',
            ]);
        }

        $fieldset->addField('user_id', 'select', [
            'label'  => __('RMA Owner'),
            'name'   => 'user_id',
            'value'  => $rma->getUserId(),
            'values' => $this->rmaUserHtml->toAdminUserOptionArray(true),
        ]);

        $fieldset->addField('status_id', 'select', [
            'label'  => __('Status'),
            'name'   => 'status_id',
            'value'  => $rma->getStatusId(),
            'values' => $this->convertDataObject->toOptionArray($this->rmaOption->getStatusList(), "id", "name")
        ]);

        $fieldset->addField('return_label', 'Mirasvit\Rma\Block\Adminhtml\Rma\Edit\Form\Element\File', [
            'label'      => __('Upload Return Label'),
            'name'       => 'return_label',
            'attachment' => $this->rmaManagement->getReturnLabel($rma),
        ]);

        if ($rma->getId()) {
            $fieldset->addField('guest_link', 'link', [
                'label'  => __('External Link'),
                'name'   => 'guest_link',
                'class'  => 'guest-link',
                'value'  => __('open'),
                'href'   => $this->rmaUrl->getGuestUrl($rma),
                'target' => '_blank',
            ]);
            $html = $this->getOtherRmasHtml($rma);
            if ($html) {
                $fieldset->addField('other_rmas', 'note', [
                    'label' => __('Other RMAs'),
                    'name'  => 'other_rmas',
                    'class' => 'other-rmas',
                    'text'  => $html,
                ]);
            }
        }

        $this->customFields->addExchangeOrders($fieldset, $rma);
        $this->customFields->addCreditmemos($fieldset, $rma);

        $this->customFields->getReturnAddress($fieldset, $rma);

        return $form->toHtml();
    }

    /**
     * @param \Mirasvit\Rma\Api\Data\RmaInterface $rma
     * @return string
     */
    protected function getOtherRmasHtml($rma)
    {
        $html = '';
        $orders = $this->rmaOrderService->getOrders($rma);
        foreach ($orders as $order) {
            if ($order->getIsOffline()) {
                return '';
            }
            $rmas = $this->rmaManagementService->getRmasByOrder($order);
            foreach ($rmas as $otherRma) {
                if ($rma->getId() != $otherRma->getId()) {
                    $url  = $this->getUrl('rma/rma/edit', ['id' => $otherRma->getId()]);
                    $html .= '<a href="' . $url . '" target="_blank">#' . $otherRma->getIncrementId() . '</a><br/>';
                }
            }
        }

        return $html;
    }

    /**
     * @param \Mirasvit\Rma\Api\Data\RmaInterface $rma
     * @return bool|\Magento\Framework\Data\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFieldForm(\Mirasvit\Rma\Api\Data\RmaInterface $rma)
    {
        return $this->customFields->getFieldForm($rma);
    }

    /**
     * Escape HTML entities
     *
     * @param string|array $data
     * @param array|null $allowedTags
     * @return string
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        //html can contain incorrect symbols which produce warrnings to log
        $internalErrors = libxml_use_internal_errors(true);
        $res = parent::escapeHtml($data, $allowedTags);
        libxml_use_internal_errors($internalErrors);
        return $res;
    }
}