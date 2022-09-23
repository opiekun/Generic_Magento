<?php
namespace WeltPixel\OwlCarouselSlider\Block\Slider;

class ConditionsProducts extends \WeltPixel\OwlCarouselSlider\Block\Slider\Products  implements \Magento\Widget\Block\BlockInterface
{

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setData('products_type', 'conditions_based_products');
    }

    public function getCacheKeyInfo()
    {
        $conditions = $this->getData('conditions')
            ? $this->getData('conditions')
            : $this->getData('conditions_encoded');

        return [
            'WELTPIXEL_PRODUCTS_LIST_WIDGET',
            $this->_storeManager->getStore()->getId(),
            $this->_storeManager->getStore()->getCurrentCurrency()->getCode(),
            $this->_design->getDesignTheme()->getId(),
            $this->getData('products_type'),
            $this->getData('category'),
            $conditions,
            json_encode($this->getRequest()->getParams())
        ];
    }

}
