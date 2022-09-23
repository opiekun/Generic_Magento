<?php
namespace ClassWallet\Payment\Plugin;  

class DisablePaymentInFront  
{    

    const CLASSWALLET = 'classwallet'; 

   /**  
    * @var \Magento\Framework\App\State  
    */  
   private $appState;  
   /**  
    * @var \Magento\Framework\App\Config\ScopeConfigInterface  
    */  
   private $scopeConfig;  
   /**  
    * @param \Magento\Framework\App\State $appState  
    */  
   public function __construct(  
     \Magento\Framework\App\State $appState,  
     \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
     \Magento\Catalog\Model\Session $catalogSession
   )  
   {  
     $this->appState        =   $appState;  
     $this->scopeConfig     =   $scopeConfig; 
     $this->catalogSession  =   $catalogSession; 
   }  
   /**  
    * @param \Magento\Payment\Model\Method\AbstractMethod $subject  
    * @param $result  
    * @return bool  
    * @throws \Magento\Framework\Exception\LocalizedException  
    */  
    public function afterIsAvailable(\Magento\Payment\Model\Method\AbstractMethod $subject, $result)  
    {  
      	$area_code = $this->appState->getAreaCode(); 
      	if ($area_code != \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {  
        	$methodCode   =   $subject->getCode();
        	$isClasswalletSession   =   $this->catalogSession->getIsClasswalletLogin();
        	if ($methodCode != self::CLASSWALLET && $isClasswalletSession) {  
          		return false;  
        	}

        	if ($methodCode == self::CLASSWALLET && !$isClasswalletSession) {  
          		return false;
        	}
      	}  
      	return $result;  
    }   
}  
