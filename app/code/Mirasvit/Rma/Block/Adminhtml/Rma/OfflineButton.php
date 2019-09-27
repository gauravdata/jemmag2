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


namespace Mirasvit\Rma\Block\Adminhtml\Rma;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class OfflineButton extends \Magento\Backend\Block\Template implements ButtonProviderInterface
{
    public function __construct(
        \Mirasvit\Rma\Api\Config\OfflineOrderConfigInterface $offlineOrderConfig,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->offlineOrderConfig = $offlineOrderConfig;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        if ($this->offlineOrderConfig->isOfflineOrdersEnabled()) {
            return [
                'label' => __('Create Offline'),
                'class' => 'primary',
                'url' => '*/*/customer',
                'sort_order' => 30,
            ];
        }

        return [];
    }
}
