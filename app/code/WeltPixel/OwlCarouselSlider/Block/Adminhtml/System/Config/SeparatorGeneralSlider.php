<?php

namespace WeltPixel\OwlCarouselSlider\Block\Adminhtml\System\Config;

/**
 * Class SeparatorGeneralSlider
 * @package WeltPixel\OwlCarouselSlider\Block\Adminhtml\System\Config
 */
class SeparatorGeneralSlider extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '
            <div class="message" style="text-align: center; margin-top: 20px;">
                <strong>' . __('General Slider Settings') . '</strong><br />
            </div>
        ';

        return $html;
    }
}
