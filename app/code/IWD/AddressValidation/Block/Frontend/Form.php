<?php

namespace IWD\AddressValidation\Block\Frontend;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use IWD\AddressValidation\Helper\Data as Helper;

/**
 * Class Form
 * @package IWD\AddressValidation\Block\Frontend
 */
class Form extends Template
{
    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var
     */
    private $response;

    public function __construct(
        Context $context,
        Helper $helper,
        array $data
    ) {
        parent::__construct($context, $data);

        $this->helper = $helper;
    }

    /**
     * @return Helper
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
        if ($this->helper->getEnable()) {
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
