<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Followupemail2\Block\Adminhtml\Event;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class CancelButton
 * @package Aheadworks\Followupemail2\Block\Adminhtml\Event
 */
class CancelButton implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label' => __('Cancel'),
            'class' => 'cancel',
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'aw_followupemail2_manage_events_form'
                                    . '.aw_followupemail2_manage_events_form.event_edit_modal',
                                'actionName' => 'closeModal'
                            ],
                        ]
                    ]
                ]
            ],
            'on_click' => '',
            'sort_order' => 20
        ];
    }
}
