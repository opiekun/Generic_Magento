<?php

namespace WeltPixel\GA4\Block\Adminhtml\System\Config;

/**
 * Class DimensionSeparator
 * @package WeltPixel\GA4\Block\Adminhtml\System\Config
 */
class DimensionSeparator extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $elementId = $element->getId();
        $elementName = ucwords(str_replace("_", " ", str_replace("weltpixel_ga4_general_separator_", "", $elementId)));

        $html = '
            <p style="text-align: center;"><strong >' . $elementName . '</strong></p>' .
            '<p style="font-size: 12px; text-align: center">(' .  __('Retrigger the Variables, Triggers and Tags Setup if you make changes here').  ')</p>

        ';

        return $html;
    }
}
