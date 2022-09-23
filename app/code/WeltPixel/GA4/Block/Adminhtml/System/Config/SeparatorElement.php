<?php

namespace WeltPixel\GA4\Block\Adminhtml\System\Config;

/**
 * Class SeparatorElement
 * @package WeltPixel\GA4\Block\Adminhtml\System\Config
 */
class SeparatorElement extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $originalData = $element->getOriginalData();

        $html = '
            <div class="message" style="text-align: center; margin-top: 20px;">
                <strong>' . $originalData['button_label']  . '</strong><br />
            </div>
        ';

        return $html;
    }
}
