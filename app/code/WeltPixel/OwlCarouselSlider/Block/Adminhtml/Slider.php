<?php

namespace WeltPixel\OwlCarouselSlider\Block\Adminhtml;

/**
 * Slider grid container
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Slider extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_slider';
        $this->_blockGroup = 'WeltPixel_OwlCarouselSlider';
        $this->_headerText = __('Sliders');
        $this->_addButtonLabel = __('Add New Slider');
        
        parent::_construct();
    }
}
