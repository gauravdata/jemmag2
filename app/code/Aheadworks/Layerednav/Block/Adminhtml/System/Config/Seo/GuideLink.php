<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Block\Adminhtml\System\Config\Seo;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class GuideLink
 * @package Aheadworks\Layerednav\Block\Adminhtml\System\Config\Seo
 */
class GuideLink extends Field
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'system/config/seo/guide_link.phtml';

    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        return $this->_decorateRowHtml($element, $this->toHtml());
    }

    /**
     * Get user guide link url
     *
     * @return string
     */
    public function getGuideLinkUrl()
    {
        return 'http://confluence.aheadworks.com'
            . '/display/EUDOC/Layered+Navigation+-+Magento+2#LayeredNavigation-Magento2-SEO';
    }
}
