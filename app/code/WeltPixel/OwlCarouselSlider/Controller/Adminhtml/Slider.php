<?php

namespace WeltPixel\OwlCarouselSlider\Controller\Adminhtml;

/**
 * Slider Abstract Action
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
abstract class Slider extends \WeltPixel\OwlCarouselSlider\Controller\Adminhtml\AbstractAction
{
    const PARAM_ID = 'id';

    /**
     * Check if admin has permissions to visit slider pages.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('WeltPixel_OwlCarouselSlider::owlcarouselslider_custom_sliders');
    }
}
