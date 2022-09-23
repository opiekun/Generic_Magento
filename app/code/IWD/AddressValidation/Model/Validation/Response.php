<?php

namespace IWD\AddressValidation\Model\Validation;

use Magento\Framework\DataObject;

/**
 * Class Response
 * @package IWD\AddressValidation\Model\Validation
 */
class Response extends DataObject
{
    /**
     * @var bool
     */
    private $isError;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * @var bool
     */
    private $isValid;

    /**
     * @var \IWD\AddressValidation\Model\Validation\Address
     */
    private $originalAddress;

    /**
     * @var \IWD\AddressValidation\Model\Validation\Address[]
     */
    private $suggestedAddresses;

    /**
     * Response constructor.
     * @param Address $address
     * @param array $data
     */
    public function __construct(
        Address $address,
        array $data = []
    ) {
        parent::__construct($data);
        $this->originalAddress = $address;
        $this->isError = false;
        $this->isValid = false;
        $this->errorMessage = '';
        $this->suggestedAddresses = [];
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @return bool
     */
    public function getIsError()
    {
        return $this->isError;
    }

    /**
     * @return bool
     */
    public function getIsValid()
    {
        return $this->isValid;
    }

    /**
     * @return Address
     */
    public function getOriginalAddress()
    {
        return $this->originalAddress;
    }

    /**
     * @return Address[]
     */
    public function getSuggestedAddresses()
    {
        return $this->suggestedAddresses;
    }

    /**
     * @param Address $address
     * @return $this
     */
    public function setOriginAddress(Address $address)
    {
        $address->updateRegionData();
        $this->originalAddress = $address;
        return $this;
    }

    /**
     * @param Address $address
     * @return $this
     */
    public function addSuggestedAddress(Address $address)
    {
        $address->updateRegionData();
        $this->suggestedAddresses[] = $address;
        return $this;
    }

    /**
     * @param $isValid
     * @return $this
     */
    public function setIsValid($isValid)
    {
        $this->isValid = $isValid;
        return $this;
    }

    /**
     * @param $message
     * @return $this
     */
    public function addError($message)
    {
        $this->isError = true;
        $this->errorMessage = $message;
        return $this;
    }

    /**
     * @param array $keys
     * @return array
     */
    public function toArray(array $keys = [])
    {
        return
        [
            'error' => $this->isError,
            'error_message' => $this->errorMessage,
            'is_valid' => $this->isValid,
            'original_address'  => $this->originalAddress->toArray(),
            'suggested_addresses' => $this->getSuggestedAddressToArray()
        ];
    }

    /**
     * @return DataObject
     */
    public function toDataObject()
    {
        $dataArray = $this->toArray();
        return new DataObject($dataArray);
    }

    /**
     * @return array
     */
    private function getSuggestedAddressToArray()
    {
        $suggested = [];
        foreach ($this->suggestedAddresses as $address) {
            $suggested[] = $address->toArray();
        }
        return $suggested;
    }
}
