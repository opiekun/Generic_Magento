<?php

namespace IWD\AddressValidation\Model;

use IWD\AddressValidation\Model\Validation\Address;
use IWD\AddressValidation\Model\Validation\Response;
use IWD\AddressValidation\Helper\Data;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class AbstractValidation
 * @package IWD\AddressValidation\Model
 */
abstract class AbstractValidation extends AbstractModel
{
    /**
     * @var Address
     */
    private $address;

    /**
     * @var Response
     */
    public $response;

    /**
     * @var Data
     */
    public $helper;

    /**
     * AbstractValidation constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Data $helper
     * @param Address $address
     * @param Response $response
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $helper,
        Address $address,
        Response $response,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->helper = $helper;
        $this->response = $response;
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    abstract public function validateAddress();

    /**
     * @return mixed
     */
    abstract public function getEnable();

    /**
     * @return \IWD\AddressValidation\Model\Validation\Response
     */
    public function getValidationResponse()
    {
        return $this->response;
    }

    /**
     * Validate address
     */
    public function validate()
    {
        try {
            $this->checkAddressData();
            $this->validateAddress();
        } catch (\Exception $e) {
            $this->response->addError($e->getMessage());
        }
    }

    /**
     * @param \IWD\AddressValidation\Model\Validation\Address|array $address
     */
    public function setAddressForValidation($address)
    {
        if (is_array($address)) {
            $this->address->setData($address);
        } else {
            $this->address = $address;
        }

        $this->response->setOriginAddress($this->address);
    }

    /**
     * @return \IWD\AddressValidation\Model\Validation\Address
     */
    public function getAddressForValidation()
    {
        return $this->address;
    }

    public function checkAddressData()
    {
        $this->getAddressForValidation()->checkAddressData();
    }

    /**
     * @param \IWD\AddressValidation\Model\Validation\Address $address
     */
    public function addSuggestedAddress($address)
    {
        $address->updateRegionData();
        $isEqual = $address->isEqualWithAddress($this->getAddressForValidation());
        if (!$isEqual) {
            if (!$this->isSuggestedAddressAdded($address)) {
                $this->response->addSuggestedAddress($address);
            }
        } else {
            $this->response->setIsValid(true);
        }
    }

    /**
     * @param $address
     * @return bool
     */
    private function isSuggestedAddressAdded($address)
    {
        $suggestedAddresses = $this->response->getSuggestedAddresses();
        foreach ($suggestedAddresses as $suggestedAddress) {
            if ($suggestedAddress->isEqualWithAddress($address)) {
                return true;
            }
        }
        return false;
    }
}
