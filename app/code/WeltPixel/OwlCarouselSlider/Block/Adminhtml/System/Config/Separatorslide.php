<?php

namespace WeltPixel\OwlCarouselSlider\Block\Adminhtml\System\Config;

/**
 * Implement
 * @category WeltPixel_OwlCarouselSlider
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel_OwlCarouselSlider Developer
 */
class Separatorslide extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '
            <div class="message" style="text-align: center; margin-top: 20px;">
                <strong>' . __('General Carousel Options') . '</strong><br />
            </div>
        ';

        return $html;
    }
}
