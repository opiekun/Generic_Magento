<?php namespace Clearsale\Integration\Block\Adminhtml;

 

class Totalbackend extends \Magento\Backend\Block\Template
{

	protected $auth;
	protected $configReader;
	protected $objectManager;
	
	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
		\Clearsale\Integration\Model\Auth\Business\AuthBusinessObject $auth,
        \Magento\Framework\App\DeploymentConfig\Reader $configReader
	) {
		$this->auth = $auth;
		$this->configReader = $configReader;
		$this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		
		parent::__construct(
	        $context
        );

	}
  
	public function getConfigActive() {
		return $this->objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('clearsale_configuration/cs_config/active',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getConfigEnvironment() {
		return $this->objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('clearsale_configuration/cs_config/environment',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getConfigClientSecret() {
		return $this->objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('clearsale_configuration/cs_config/clientsecret',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getConfigClientId() {
		return $this->objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('clearsale_configuration/cs_config/clientid',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getConfigKey() {
		return $this->objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('clearsale_configuration/cs_config/key',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getLogin() {
		$environment = $this->objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('clearsale_configuration/cs_config/environment',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$authResponse = $this->auth->login($environment, null);	
		if($authResponse) {
			return $authResponse->Token->Value;
		}
		else {
			return null;
		}
 	
	}

	public function getAdminBaseUrl(){ 
        $config = $this->configReader->load();
        $adminSuffix = $config['backend']['frontName'];
        return $this->getBaseUrl() . $adminSuffix;
    }

}