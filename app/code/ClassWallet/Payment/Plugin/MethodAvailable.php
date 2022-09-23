<?php

namespace ClassWallet\Payment\Plugin;

class MethodAvailable
{
    const CLASSWALLET = 'classwallet';
 	public function __construct(
		\Magento\Catalog\Model\Session $catalogSession,
     	\Magento\Framework\App\State $appState
	)
	{
     	$this->catalogSession = $catalogSession; 
     	$this->appState = $appState;  
	}
    /**
     * @param Magento\Payment\Model\MethodList $subject
     * @param $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAvailableMethods(\Magento\Payment\Model\MethodList $subject, $availableMethods)
    {
      	$area_code = $this->appState->getAreaCode(); 
      	if ($area_code != \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {  
			$isClasswalletSession = $this->catalogSession->getIsClasswalletLogin();
			foreach ($availableMethods as $key=>$method) {
				$methodCode = $method->getCode();
				if ($methodCode != self::CLASSWALLET && $isClasswalletSession) {
					unset($availableMethods[$key]);
				}

				if ($methodCode == self::CLASSWALLET && !$isClasswalletSession) {
					unset($availableMethods[$key]);
				}
			}
			return $availableMethods;
		}
   } 
}
