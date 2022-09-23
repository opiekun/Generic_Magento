<?php

namespace IWD\AddressValidation\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Documentation
 * @package IWD\AddressValidation\Block\Adminhtml\System\Config
 */
class Documentation extends Field
{
    private $userGuideUrl = "https://iwdagency.com/help/m2-address-validation/address-2-settings";

    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return sprintf(
            "<span style='margin-bottom:-8px; display:block;'><a href='%s' target='_blank'>%s</a></span>",
            $this->userGuideUrl,
            __("User Guide")
        );
    }
}
