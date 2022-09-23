<?php
namespace Clearsale\Integration\Model\Auth\Business;


class AuthBusinessObject
{

	public $Http;
    protected $scopeConfig;
	protected $totalUtilsHttpHelperFactory;
	protected $totalAuthEntityRequestAuthFactory;
    protected $logger;

    function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Clearsale\Integration\Model\Utils\HttpHelperFactory $totalUtilsHttpHelperFactory,
        \Clearsale\Integration\Model\Auth\Entity\RequestAuthFactory $totalAuthEntityRequestAuthFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->Http = $totalUtilsHttpHelperFactory->create();
        $this->totalAuthEntityRequestAuthFactory = $totalAuthEntityRequestAuthFactory;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
	}

public function login($environment, $storeId) {

		$url = $environment."api/auth/login/";
		
		$authRequest = $this->totalAuthEntityRequestAuthFactory->create();
		$authRequest->Login->ApiKey = $this->scopeConfig->getValue('clearsale_configuration/cs_config/key',\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
		$authRequest->Login->ClientID = $this->scopeConfig->getValue('clearsale_configuration/cs_config/clientid',\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
		$authRequest->Login->ClientSecret = $this->scopeConfig->getValue('clearsale_configuration/cs_config/clientsecret',\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
				
		$response = $this->Http->postData($authRequest, $url);	
                
		$credentials = "";
		
		if($response->HttpCode == 200)
		{
			$credentials = json_decode($response->Body);
		}
		
		return $credentials;
	}

public function logout($environment) {
		$authRequest = $this->totalAuthEntityRequestAuthFactory->create();
		$authRequest->Login->ApiKey = $this->scopeConfig->getValue('clearsale_configuration/cs_config/key',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$authRequest->Login->ClientID = $this->scopeConfig->getValue('clearsale_configuration/cs_config/clientid',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$authRequest->Login->ClientSecret = $this->scopeConfig->getValue('clearsale_configuration/clientsecret/key',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$url = $environment."api/auth/logout/";
		$response = $this->Http->postData($authRequest, $url);
		     
                $credentials = "";
                
                if($response->HttpCode == 200)
                {
                    $credentials = json_decode($response->Body);
                }
                
		return $credentials;
	}
}
   