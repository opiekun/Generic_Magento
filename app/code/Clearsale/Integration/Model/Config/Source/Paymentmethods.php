<?php

namespace Clearsale\Integration\Model\Config\Source;

use \Magento\Payment\Helper\Data;
use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Payment\Model\Config;

class Paymentmethods extends \Magento\Framework\DataObject 
    implements \Magento\Framework\Option\ArrayInterface
{
	protected $_paymentHelper;
     
    public function __construct(
		Context $context,
		Data $paymentHelper
    ) {
		$this->context = $context;
		$this->_paymentHelper = $paymentHelper;
    }
  
    public function toOptionArray()
    {
        $payments = $this->_paymentHelper->getPaymentMethods();
		$methods = array();
		
		foreach ($payments as $paymentCode => $paymentModel) {
            if(array_key_exists("title", $paymentModel))
            {
                $methods[$paymentCode] = array(
                    'label' => $paymentModel["title"].' [code: '.$paymentCode.']',
                    'value' => $paymentCode
                );
            }
		}
		
        return $methods;
    }
}