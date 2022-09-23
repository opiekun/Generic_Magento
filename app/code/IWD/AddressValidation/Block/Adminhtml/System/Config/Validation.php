<?php

namespace IWD\AddressValidation\Block\Adminhtml\System\Config;

use IWD\AddressValidation\Helper\Data;
use IWD\AddressValidation\Model\Validation\Validator;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;

/**
 * Class Validation
 * @package IWD\AddressValidation\Block\Adminhtml\System\Config
 */
class Validation extends Field
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var \IWD\AddressValidation\Model\Google\Validation|\IWD\AddressValidation\Model\Ups\Validation|\IWD\AddressValidation\Model\Usps\Validation
     */
    private $addressValidator;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $color;

    /**
     * Validation constructor.
     * @param Context $context
     * @param Data $helper
     * @param Validator $validator
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        Validator $validator,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->addressValidator = $validator->getValidator();
    }

    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->validateApiCredentials();
        return "<span style='margin-bottom:-8px; display:block; color:{$this->color}'>{$this->message}</span>" .
            "<style>#row_iwd_opc_addresvalidation_opc_api_settings_validtion label>span{display:none}" .
            "#row_iwd_addressvalidation_api_settings_validtion label span{display:none}</style>";
    }

    private function validateApiCredentials()
    {
        if ($this->helper->getEnable() == 0) {
            $this->color = '#D40707';
            $this->message = __("Extension is disabled");
            return;
        }

        if ($this->validateApi() == false) {
            $this->color = '#D40707';
            $this->message = __("Incorrect API credentials");
            return;
        }

        $this->color = '#059147';
        $this->message = __("Validation settings are correct");
    }

    /**
     * @return bool
     */
    private function validateApi()
    {
        $request = [
            "street" => "251 Florida St",
            "city" => "BATON ROUGE",
            "country_id" => "US",
            "region_id" => "28",
            "postcode" => "70801"
        ];

        $this->addressValidator->setAddressForValidation($request);
        $this->addressValidator->validate();
        $response = $this->addressValidator->getValidationResponse()->toDataObject();

        $suggested = $response->getSuggestedAddresses();

        if ($response->getError() == true || count($suggested) == 0) {
            return false;
        }

        return true;
    }
}
