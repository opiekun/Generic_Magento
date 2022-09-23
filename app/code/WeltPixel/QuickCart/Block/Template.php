<?php
namespace WeltPixel\QuickCart\Block;

/**
 * Class Template
 * @package WeltPixel\QuickCart\Block
 */
class Template extends \Magento\Framework\View\Element\Template
{
    /**
     * @param $group
     * @param $field
     * @return mixed
     */
    public function getValuesConfig($group, $field)
    {
        return $this->_scopeConfig->getValue('weltpixel_quick_cart/' . $group . '/' . $field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}