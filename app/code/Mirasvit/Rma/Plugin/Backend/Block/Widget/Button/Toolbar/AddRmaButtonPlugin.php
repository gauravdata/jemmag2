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


namespace Mirasvit\Rma\Plugin\Backend\Block\Widget\Button\Toolbar;

use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Button\Toolbar as ToolbarContext;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\AbstractBlock;

class AddRmaButtonPlugin
{
    public function __construct(
        Registry $coreRegistry
    ) {
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @param ToolbarContext $toolbar
     * @param AbstractBlock $context
     * @param ButtonList $buttonList
     * @return array
     */
    public function beforePushButtons(
        ToolbarContext $toolbar,
        \Magento\Framework\View\Element\AbstractBlock $context,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
    ) {
        if (!$context instanceof \Magento\Sales\Block\Adminhtml\Order\View) {
            return [$context, $buttonList];
        }

        $orderId = $this->getOrder()->getId();
        $buttonList->add('order_review',
            [
                'label' => __('Add RMA'),
                'onclick' => 'setLocation(\'' . $context->getUrl('rma/rma/add/', ['orders_id' => $orderId]) . '\')',
                'class' => 'add-rma',
                'sort_order' => (count($buttonList->getItems()) + 1) * 10,
            ]
        );

        return [$context, $buttonList];
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    private function getOrder()
    {
        return $this->coreRegistry->registry('sales_order');
    }
}