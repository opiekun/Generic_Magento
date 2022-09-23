<?php

namespace IWD\AddressValidation\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use IWD\AddressValidation\Helper\Data;
use IWD\AddressValidation\Model\Validation\Validator;

/**
 * Class Popup
 * @package IWD\AddressValidation\Block\Adminhtml
 */
class Popup extends Template
{
    /**
     * @var \IWD\AddressValidation\Helper\Data
     */
    private $helper;

    /**
     * @var \IWD\AddressValidation\Model\Google\Validation|\IWD\AddressValidation\Model\Ups\Validation|\IWD\AddressValidation\Model\Usps\Validation
     */
    private $addressValidator;

    /**
     * @param Template\Context $context
     * @param Data $helper
     * @param Validator $addressValidator
     * @param array $data
     * @throws \Exception
     */
    public function __construct(
        Context $context,
        Data $helper,
        Validator $addressValidator,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->addressValidator = $addressValidator->getValidator();

        parent::__construct($context, $data);
    }

    /**
     * @return Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * @return string
     */
    public function getValidateUrl()
    {
        return $this->_urlBuilder->getUrl('address_validation/ajax/validation');
    }

    /**
     * @return mixed
     */
    public function getAllowNotValidAddress()
    {
        return $this->getHelper()->getAllowNotValidAddress();
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if ($this->isEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * @return bool
     */
    private function isEnabled()
    {
        return $this->getHelper()->getEnable() && $this->addressValidator->getEnable();
    }
}
