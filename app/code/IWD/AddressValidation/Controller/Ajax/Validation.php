<?php

namespace IWD\AddressValidation\Controller\Ajax;

use IWD\AddressValidation\Model;
use IWD\AddressValidation\Helper\Data;
use IWD\AddressValidation\Model\Validation\Validator;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Module\Manager;

/**
 * Class Validation
 * @package IWD\AddressValidation\Controller\Ajax
 */
class Validation extends Action
{
    /**
     * @var Model\Google\Validation|Model\Ups\Validation|Model\Usps\Validation
     */
    private $validator;
	
	
	public $helper;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    public $moduleManager;


    /**
     * Validation constructor.
     * @param Context $context
     * @param Validator $validator
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Validator $validator,
        PageFactory $resultPageFactory,
        Manager $moduleManager,
		Data $helper
    )
    {
        parent::__construct($context);
        $this->validator = $validator->getValidator();
        $this->resultPageFactory = $resultPageFactory;
        $this->moduleManager = $moduleManager;
		$this->_helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->setAddressForValidation();

        $this->validator->validate();

        return $this->getJsonResponse();
    }

    private function setAddressForValidation()
    {
        $request = $this->getRequest()->getParams();
        $this->validator->setAddressForValidation($request);
    }

    private function getJsonResponse()
    {
        $response = $this->prepareResponse();

        /**
         * @var \Magento\Framework\Controller\Result\Json $resultJson
         */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData($response);
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    private function prepareResponse()
    {
        $response = $this->validator->getValidationResponse()->toDataObject();
        if ($response->getIsValid()) {
            return $response;
        }

        return $this->appendModalToResponse($response);
    }

    /**
     * @param $response
     * @return mixed
     */
    private function appendModalToResponse($response)
    {
        $resultPage = $this->resultPageFactory->create();
				
        /**
         * @var $block \IWD\AddressValidation\Block\Frontend\Form
         */
        $block = $resultPage->getLayout()->getBlock('iwdAddressValidationForm');
        if (!empty($block)) {
            $validationResponse = $this->validator->getValidationResponse();
            if ($this->moduleManager->isEnabled('IWD_Opc')
                && $this->moduleManager->isOutputEnabled('IWD_Opc')
				&& $this->_helper->getOpcCheckoutSuiteDesign()
				&& $this->_helper->getIwdOpcCheckoutSuite()
            ) {
                $block->setTemplate('opc_form.phtml');
            }

            $content = $block
                ->setValidationResponse($validationResponse)
                ->toHtml();

            $response->setModalContent($content);
        }

        return $response;
    }
}
