<?php
/**
 * Copyright © 2016 MB Vienas bitas. All rights reserved.
 * @website    www.magetrend.com
 * @package    MT Email for M2
 * @author     Edvinas Stulpinas <edwin@magetrend.com>
 */

namespace Magetrend\Email\Block\Email\Block\Quote\Items;

class DefaultItem extends \Magetrend\Email\Block\Email\Block\Sales\Items\Order\DefaultOrder
{
    protected function _beforeToHtml()
    {
        return $this;
    }
}
