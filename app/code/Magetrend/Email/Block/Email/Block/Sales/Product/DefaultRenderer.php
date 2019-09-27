<?php
/**
 * Copyright © 2016 MB Vienas bitas. All rights reserved.
 * @website    www.magetrend.com
 * @package    MT Email for M2
 * @author     Edvinas Stulpinas <edwin@magetrend.com>
 */

namespace Magetrend\Email\Block\Email\Block\Sales\Product;

class DefaultRenderer extends \Magetrend\Email\Block\Email\Block\Template
{
    private $product = [];
    /**
     * @var \Magento\Catalog\Block\Product\ImageBuilder
     */
    public $imageBuilder;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    public $productHelper;

    public $priceCurrency;

    public $appEmulation;

    public $blockFactory;

    public $productFactory;

    /**
     * DefaultOrder constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Catalog\Helper\Output $productHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = []
    ) {
        $this->imageBuilder = $imageBuilder;
        $this->productHelper = $productHelper;
        $this->priceCurrency = $priceCurrency;
        $this->blockFactory = $blockFactory;
        $this->appEmulation = $appEmulation;
        $this->productFactory = $productFactory;
        parent::__construct($context, $data);
    }

    /**
     * Returns item image html
     *
     * @param $item
     * @return string
     */
    public function getImage()
    {
        $product = $this->getProduct();
        if (!$product || empty($product->getImage())) {
            return $this->getProductImagePlaceholder();
        }

        $imageUrl = $this->imageBuilder->setProduct($product)
            ->setImageId('category_page_grid')
            ->create()->getImageUrl();
        return $imageUrl;
    }

    public function getProductImagePlaceholder()
    {
        return $this->getViewFileUrl('Magento_Catalog::images/product/placeholder/small_image.jpg');
    }

    public function getFormatedPrice()
    {
        $product = $this->getProduct();
        return $this->priceCurrency->format(
            $product->getPrice(),
            false,
            \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION,
            $product->getStore()
        );
    }

    public function getProduct()
    {
        $productId = $this->getData('product')->getId();
        if (!isset($this->product[$productId])) {
            $this->product[$productId] = $this->productFactory->create()->load($productId);
        }

        return $this->product[$productId];
    }
}