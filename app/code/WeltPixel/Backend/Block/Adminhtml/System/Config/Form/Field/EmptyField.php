<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_Backend
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\Backend\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class EmptyField
 * @package WeltPixel\Backend\Block\System\Config\Form\Field
 */
class EmptyField extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(AbstractElement $element)
    {
        return '';

    }
}
