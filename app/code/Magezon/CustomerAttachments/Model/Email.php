<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_CustomerAttachments
 * @copyright Copyright (C) 2018 Magezon (https://magezon.com)
 */

namespace Magezon\CustomerAttachments\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\LayoutInterface;

class Email
{
	const XML_PATH_NEW_ATTACHMENT_EMAIL_TEMPLATE = 'customerattachments/email/new_attachment';
	
	const XML_PATH_CONTACT_EMAIL                 = 'customerattachments/email/contact_email';
	
	const XML_PATH_EMAIL_IDENTITY                = 'customerattachments/email/sender_email_identity';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var DataObjectProcessor
     */
    protected $dataProcessor;

    /**
     * @var CustomerViewHelper
     */
    protected $customerViewHelper;

    /**
     * @var LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $_appEmulation;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @param StoreManagerInterface              $storeManager       
     * @param CustomerRegistry                   $customerRegistry   
     * @param ScopeConfigInterface               $scopeConfig        
     * @param TransportBuilder                   $transportBuilder   
     * @param DataObjectProcessor                $dataProcessor      
     * @param CustomerViewHelper                 $customerViewHelper 
     * @param LayoutInterface                    $layout             
     * @param \Magento\Store\Model\App\Emulation $appEmulation       
     * @param \Magento\Framework\App\State       $appState           
     */
	public function __construct(
		StoreManagerInterface $storeManager,
		CustomerRegistry $customerRegistry,
		ScopeConfigInterface $scopeConfig,
		TransportBuilder $transportBuilder,
		DataObjectProcessor $dataProcessor,
		CustomerViewHelper $customerViewHelper,
		LayoutInterface $layout,
		\Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Framework\App\State $appState
	) {
		$this->storeManager       = $storeManager;
		$this->customerRegistry   = $customerRegistry;
		$this->scopeConfig        = $scopeConfig;
		$this->transportBuilder   = $transportBuilder;
		$this->dataProcessor      = $dataProcessor;
		$this->customerViewHelper = $customerViewHelper;
		$this->_layout           = $layout;
        $this->_appEmulation     = $appEmulation;
        $this->_appState         = $appState;
	}

	public function sendNewAttachment(
		$customer,
		$attachments,
		$storeId = '0',
		$sendemailStoreId = null
	) {

		if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId($customer, $sendemailStoreId);
        }

		$store             = $this->storeManager->getStore($customer->getStoreId());
		$customerEmailData = $this->getFullCustomerObject($customer);
		$contactEmail      = $this->scopeConfig->getValue(self::XML_PATH_CONTACT_EMAIL, ScopeInterface::SCOPE_STORE, $storeId);

        if (!$contactEmail) {
        	$sender = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_IDENTITY, ScopeInterface::SCOPE_STORE, $storeId);
        	$contactEmail = $this->scopeConfig->getValue('trans_email/ident_' . $sender . '/email', ScopeInterface::SCOPE_STORE, $storeId);
    	}

    	$this->_appEmulation->startEnvironmentEmulation($storeId);
	    	$fileHtml = $this->_layout->createBlock("Magento\Framework\View\Element\Template")
	    	->setData('area', Area::AREA_FRONTEND)
	        ->setTemplate('Magezon_CustomerAttachments::email/attachments.phtml')
	        ->setAttachments($attachments)
	        ->setStore($store)
	        ->setCustomer($customer)
	        ->toHtml();
        $this->_appEmulation->stopEnvironmentEmulation();

        $this->sendEmailTemplate(
            $customer,
            self::XML_PATH_NEW_ATTACHMENT_EMAIL_TEMPLATE,
            self::XML_PATH_EMAIL_IDENTITY,
            [
                'customer'       => $customerEmailData,
                'store'          => $store,
                'number_of_file' => count($attachments),
                'contact_email'  => $contactEmail,
                'file_html'      => $fileHtml
            ],
            $storeId
        );

        return $this;

	}

    /**
     * Send corresponding email template
     *
     * @param CustomerInterface $customer
     * @param string $template configuration path of email template
     * @param string $sender configuration path of email identity
     * @param array $templateParams
     * @param int|null $storeId
     * @param string $email
     * @return $this
     * @deprecated 100.1.0
     */
    protected function sendEmailTemplate(
        $customer,
        $template,
        $sender,
        $templateParams = [],
        $storeId = null,
        $email = null
    ) {
        $templateId = $this->scopeConfig->getValue($template, ScopeInterface::SCOPE_STORE, $storeId);
        if ($email === null) {
            $email = $customer->getEmail();
        }

        $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
            ->setTemplateVars($templateParams)
            ->setFrom($this->scopeConfig->getValue($sender, ScopeInterface::SCOPE_STORE, $storeId))
            ->addTo($email, $this->customerViewHelper->getCustomerName($customer))
            ->getTransport();

        $transport->sendMessage();

        return $this;
    }

    /**
     * Get either first store ID from a set website or the provided as default
     *
     * @param CustomerInterface $customer
     * @param int|string|null $defaultStoreId
     * @return int
     * @deprecated 100.1.0
     */
    protected function _getWebsiteStoreId($customer, $defaultStoreId = null)
    {
        if ($customer->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
            reset($storeIds);
            $defaultStoreId = current($storeIds);
        }
        return $defaultStoreId;
    }

    /**
     * Create an object with data merged from Customer and CustomerSecure
     *
     * @param CustomerInterface $customer
     * @return Data\CustomerSecure
     * @deprecated 100.1.0
     */
    protected function getFullCustomerObject($customer)
    {
        // No need to flatten the custom attributes or nested objects since the only usage is for email templates and
        // object passed for events
        $mergedCustomerData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerData = $this->dataProcessor
            ->buildOutputDataArray($customer, \Magento\Customer\Api\Data\CustomerInterface::class);
        $mergedCustomerData->addData($customerData);
        $mergedCustomerData->setData('name', $this->customerViewHelper->getCustomerName($customer));
        return $mergedCustomerData;
    }
}
