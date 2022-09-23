<?php

namespace IWD\AddressValidation\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use IWD\AddressValidation\Helper\Data;

/**
 * Class Form
 * @package IWD\AddressValidation\Block\Adminhtml
 */
class Form extends Template
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var
     */
    private $response;

    /**
     * Form constructor.
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        array $data
    ) {
        parent::__construct($context, $data);

        $this->helper = $helper;
    }

    /**
     * @return Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * @return string
     */
    public function getValidateUrl()
    {
        return $this->_urlBuilder->getUrl('address_validation/ajax/validation');
    }

    /**
     * {@inheritdoc}
     */
    public function _toHtml()
    {
        if ($this->getHelper()->getEnable()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * @param $response
     * @return $this
     */
    public function setValidationResponse($response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValidationResponse()
    {
        return $this->response;
    }
}
