<?php namespace Clearsale\Integration\Block\Frontend;

use Magento\Framework\View\Element\Template;

class Mapper extends Template
{

	protected $configReader;
	protected $objectManager;

    public function __construct(
        Template\Context $context
    ) {
		$this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct(
            $context
        );
    }

    public function getConfigClientId()
    {
        return $this->objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('clearsale_configuration/cs_config/clientid', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getConfigActive()
    {
		return $this->objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('clearsale_configuration/cs_config/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

}