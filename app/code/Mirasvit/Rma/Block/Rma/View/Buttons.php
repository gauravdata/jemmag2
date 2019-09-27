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



namespace Mirasvit\Rma\Block\Rma\View;

class Buttons extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Mirasvit\Rma\Api\Service\Field\FieldManagementInterface $fieldManagement,
        \Mirasvit\Rma\Service\Rma\RmaAdapter $rmaAdapter,
        \Magento\Framework\Registry $registry,
        \Mirasvit\Rma\Api\Service\Rma\ShippingManagementInterface $shippingManagement,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->fieldManagement    = $fieldManagement;
        $this->rmaAdapter         = $rmaAdapter;
        $this->registry           = $registry;
        $this->context            = $context;
        $this->shippingManagement = $shippingManagement;

        parent::__construct($context, $data);
    }

    /**
     * @return \Mirasvit\Rma\Api\Data\RmaInterface
     */
    public function getRma()
    {
        $rma =  $this->registry->registry('current_rma');
        $this->rmaAdapter->setData($rma->getData());

        return $rma;
    }

    /**
     * @return bool
     */
    public function isShowShippingBlock()
    {
        return $this->shippingManagement->isShowShippingBlock($this->getRma());
    }

    /**
     * @return bool
     */
    public function isRequireShippingConfirmation()
    {
        return $this->shippingManagement->isRequireShippingConfirmation($this->getRma());
    }

    /**
     * @return string
     */
    public function getShippingConfirmation()
    {
        $str = $this->shippingManagement->getShippingConfirmationText($this->context->getStoreManager()->getStore());
        $str = str_replace('"', '\'', $str);

        return $str;
    }

    /**
     * @return \Mirasvit\Rma\Model\Field[]
     */
    public function getShippingConfirmationFields()
    {
        return $this->fieldManagement->getShippingConfirmationFields();
    }

    /**
     * @param \Mirasvit\Rma\Model\Field $field
     *
     * @return string
     */
    public function getFieldInputHtml(\Mirasvit\Rma\Model\Field $field)
    {
        return $this->fieldManagement->getInputHtml($field);
    }

    /**
     * @return \Mirasvit\Rma\Service\Rma\RmaAdapter
     */
    public function getRmaAdapter()
    {
        return $this->rmaAdapter;
    }
}
