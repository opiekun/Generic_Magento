<?php

namespace WeltPixel\OwlCarouselSlider\Helper;

/**
 * Helper Data
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * [__construct description].
     *
     * @param \Magento\Framework\App\Helper\Context          $context
     * @param \Magento\Store\Model\StoreManagerInterface     $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl
    ) {
        parent::__construct($context);

        $this->_backendUrl = $backendUrl;
    }

    /**
     * get Slider Banner Url
     * @return string
     */
    public function getSliderBannerUrl()
    {
        return $this->_backendUrl->getUrl('*/*/banners', ['_current' => true]);
    }

    /**
     * Retrieve the related products.
     *
     * @param $product
     * @return mixed
     */
    public function getRelatedProducts($product)
    {
        return $product->getRelatedProducts();
    }

    /**
     * Retrieve the up-sell products.
     *
     * @param $product
     * @return mixed
     */
    public function getUpsellProducts($product)
    {
        return $product->getUpSellProducts();
    }

    /**
     * Retrieve the cross-sell products.
     *
     * @param $product
     * @return mixed
     */
    public function getCrosssellProducts($product)
    {
        return $product->getCrossSellProducts();
    }
}
