<?php

namespace IWD\AddressValidation\Block\Frontend;

use IWD\AddressValidation\Helper\Data;
use IWD\AddressValidation\Model\Validation\Validator;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Popup
 * @package IWD\AddressValidation\Block\Frontend
 */
class Popup extends Template
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var \IWD\AddressValidation\Model\Google\Validation|\IWD\AddressValidation\Model\Ups\Validation|\IWD\AddressValidation\Model\Usps\Validation
     */
    private $addressValidator;

    public $addressValidationProcessor;

    /**
     * Popup constructor.
     * @param Context $context
     * @param Data $helper
     * @param Validator $addressValidator
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        Validator $addressValidator,
        array $data
    )
    {
        $this->helper = $helper;
        $this->addressValidator = $addressValidator->getValidator();
        $this->addressValidationProcessor = $addressValidator->getValidationMode();

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
     * {@inheritdoc}
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

    public function getAddressValidationProcessor()
    {
        return $this->addressValidationProcessor;
    }
}
