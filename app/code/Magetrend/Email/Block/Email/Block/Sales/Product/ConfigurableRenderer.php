<?php
/**
 * Copyright © 2016 MB Vienas bitas. All rights reserved.
 * @website    www.magetrend.com
 * @package    MT Email for M2
 * @author     Edvinas Stulpinas <edwin@magetrend.com>
 */

namespace Magetrend\Email\Block\Email\Block\Sales\Product;

class  ConfigurableRenderer extends \Magetrend\Email\Block\Email\Block\Sales\Product\DefaultRenderer
{
    public function getFormatedPrice()
    {
        $product = $this->getProduct();
        return $this->priceCurrency->format(
            $product->getFinalPrice(),
            false,
            \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION,
            $product->getStore()
        );
    }
}