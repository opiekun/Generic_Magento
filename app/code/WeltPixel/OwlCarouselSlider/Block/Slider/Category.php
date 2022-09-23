<?php
namespace WeltPixel\OwlCarouselSlider\Block\Slider;

class Category extends \WeltPixel\OwlCarouselSlider\Block\Slider\Products  implements \Magento\Widget\Block\BlockInterface
{

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setData('products_type', 'category_products');
    }

    public function getCacheKeyInfo()
    {
        return [
            'WELTPIXEL_PRODUCTS_LIST_WIDGET',
            $this->_storeManager->getStore()->getId(),
            $this->_storeManager->getStore()->getCurrentCurrency()->getCode(),
            $this->_design->getDesignTheme()->getId(),
            $this->getData('products_type'),
            $this->getData('category'),
            json_encode($this->getRequest()->getParams())
        ];
    }

}
