<?php
namespace ClassWallet\Payment\Block;

use Magento\Framework\View\Element\Template\Context;
 
class Button extends  \Magento\Framework\View\Element\Template
{

	/**
   * @var \Magento\Framework\App\Config\ScopeConfigInterface
   */
   protected $scopeConfig;

   /**
   * Button config path
   */
   const BUTTON_CONFIG_PATH       = 'payment/classwallet/enable_cart_button';
   const DEFAULT_SHIPPING_METHOD  = 'payment/classwallet/default_shipping_method';
   const METHOD_CONFIG_PATH       = 'payment/classwallet/active';

	public function __construct(
	 	Context $context,
	 	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    \Magento\Customer\Model\Session $session,
    \Magento\Catalog\Model\Session $catalogSession
	) {
        $this->_customerSession =   $session;
        $this->scopeConfig      =   $scopeConfig;
        $this->catalogSession   =   $catalogSession; 
		    parent::__construct($context);
    }

    public function isButtonEnabled() {
    	$storeScope 		        = 	\Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    	$cartBtnEnabled 	      =	  $this->scopeConfig->getValue(self::BUTTON_CONFIG_PATH, $storeScope);
    	$methodEnabled 		      =	  $this->scopeConfig->getValue(self::METHOD_CONFIG_PATH, $storeScope);
      $isClasswalletSession   =   $this->catalogSession->getIsClasswalletLogin();
    	if($cartBtnEnabled && $methodEnabled && $this->_customerSession->isLoggedIn() && $isClasswalletSession){
    		return true;
    	}
    }

    public function defaultShippingMethod() {
      $storeScope             =   \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
      $defaultShippingMtd     =   $this->scopeConfig->getValue(self::DEFAULT_SHIPPING_METHOD, $storeScope);
      return $defaultShippingMtd;
    }
}