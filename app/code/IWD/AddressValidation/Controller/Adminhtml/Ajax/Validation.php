<?php

namespace IWD\AddressValidation\Controller\Adminhtml\Ajax;

use IWD\AddressValidation\Model;
use IWD\AddressValidation\Model\Validation\Validator;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Validation
 * @package IWD\AddressValidation\Controller\Adminhtml\Ajax
 */
class Validation extends Action
{
    /**
     * @var Model\Google\Validation|Model\Ups\Validation|Model\Usps\Validation
     */
    private $validator;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * Validation constructor.
     * @param Context $context
     * @param Validator $validator
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Validator $validator,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->validator = $validator->getValidator();
        $this->resultPageFactory = $resultPageFactory;
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

    /**
     * @return $this
     */
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
     * @return \Magento\Framework\DataObject|mixed
     */
    private function prepareResponse()
    {
        $response = $this->validator->getValidationResponse()->toDataObject();

        if (strpos($response->getData('error_message'), '-2147219401')) {
            $response->setData('error', false);
        }
        if ($response->getIsValid() || $response->getError()) {
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
         * @var $block \IWD\AddressValidation\Block\Adminhtml\Form
         */
        $block = $resultPage->getLayout()->getBlock('iwdAddressValidationFormAdmin');
        if (!empty($block)) {
            $validationResponse = $this->validator->getValidationResponse();
            $content = $block
                ->setValidationResponse($validationResponse)
                ->toHtml();

            $response->setModalContent($content);
        }

        return $response;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
