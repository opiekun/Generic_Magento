<?php

namespace IWD\AddressValidation\Helper;

use Magento\Config\Model\Config\Backend\Encrypted;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package IWD\AddressValidation\Helper
 */
class Data extends AbstractHelper
{
    const ENABLE                  = 'iwd_addressvalidation/general/enable';
    const ALLOW_NOT_VALID_ADDRESS = 'iwd_addressvalidation/general/allow_not_valid_address';

    const VALIDATION_MODE         = 'iwd_addressvalidation/api_settings/mode';

    const CONTENT_HEADER            = 'iwd_addressvalidation/content/header';
    const CONTENT_MESSAGE           = 'iwd_addressvalidation/content/message';
    const CONTENT_ORIGIN_ADDRESS    = 'iwd_addressvalidation/content/origin_address';
    const CONTENT_SUGGESTED_ADDRESS = 'iwd_addressvalidation/content/suggested_address';

    const UPS_TEST_MODE           = 'iwd_addressvalidation/api_settings/ups_test_mode';
    const UPS_LOGIN               = 'iwd_addressvalidation/api_settings/ups_login';
    const UPS_PASSWORD            = 'iwd_addressvalidation/api_settings/ups_password';
    const UPS_ACCESS_KEY          = 'iwd_addressvalidation/api_settings/ups_access_key';
    const UPS_SHOW_ADDRESS_TYPE   = 'iwd_addressvalidation/api_settings/ups_show_address_type';

    const USPS_TEST_MODE          = 'iwd_addressvalidation/api_settings/usps_test_mode';
    const USPS_ACCOUNT_ID         = 'iwd_addressvalidation/api_settings/usps_account_id';

    const GOOGLE_API_KEY          = 'iwd_addressvalidation/api_settings/google_key';
	
	const XML_PATH_IWD_CHECKOUT_SUITE_DESIGN = 'iwd_opc/general/checkout_suite_design';
	const XML_PATH_ENABLE_IWD_OPC = 'iwd_opc/general/enable';

    /**
     * @var Encrypted
     */
    private $encrypted;

    /**
     * Data constructor.
     * @param Context $context
     * @param Encrypted $encrypted
     */
    public function __construct(
        Context $context,
        Encrypted $encrypted
    ) {
        parent::__construct($context);
        $this->encrypted = $encrypted;
    }

    /**
     * @return mixed
     */
    public function getEnable()
    {
        return $this->scopeConfig->getValue(self::ENABLE, ScopeInterface::SCOPE_STORE);
    }
	
	public function getIwdOpcCheckoutSuite()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLE_IWD_OPC);
    }
	
	public function getOpcCheckoutSuiteDesign()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_IWD_CHECKOUT_SUITE_DESIGN);
    }

    /**
     * @return mixed
     */
    public function getAllowNotValidAddress()
    {
        return $this->scopeConfig->getValue(self::ALLOW_NOT_VALID_ADDRESS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getValidationMode()
    {
        return $this->scopeConfig->getValue(self::VALIDATION_MODE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getUpsTestMode()
    {
        return $this->scopeConfig->getValue(self::UPS_TEST_MODE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getUpsLogin()
    {
        return $this->scopeConfig->getValue(self::UPS_LOGIN, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getUpsPassword()
    {
        return $this->encrypted->processValue(
            $this->scopeConfig->getValue(self::UPS_PASSWORD, ScopeInterface::SCOPE_STORE)
        );
    }

    /**
     * @return mixed
     */
    public function getUpsAccessKey()
    {
        return $this->scopeConfig->getValue(self::UPS_ACCESS_KEY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getUpsShowAddressType()
    {
        return $this->scopeConfig->getValue(self::UPS_SHOW_ADDRESS_TYPE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getUspsTestMode()
    {
        return $this->scopeConfig->getValue(self::USPS_TEST_MODE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getUspsAccountId()
    {
        return $this->scopeConfig->getValue(self::USPS_ACCOUNT_ID, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getGoogleApiKey()
    {
        return $this->scopeConfig->getValue(self::GOOGLE_API_KEY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getContentHeader()
    {
        return $this->scopeConfig->getValue(self::CONTENT_HEADER, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getContentMessage()
    {
        return $this->scopeConfig->getValue(self::CONTENT_MESSAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getContentOriginAddress()
    {
        return $this->scopeConfig->getValue(self::CONTENT_ORIGIN_ADDRESS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getContentSuggestedAddress()
    {
        return $this->scopeConfig->getValue(self::CONTENT_SUGGESTED_ADDRESS, ScopeInterface::SCOPE_STORE);
    }
}
