<?php

namespace IWD\AddressValidation\Model\Usps;

use IWD\AddressValidation\Helper\Data;
use IWD\AddressValidation\Model\AbstractValidation;
use IWD\AddressValidation\Model\Validation\Address;
use IWD\AddressValidation\Model\Validation\Response;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class Validation
 * @package IWD\AddressValidation\Model\Usps
 */
class Validation extends AbstractValidation
{
    /**
     * @var USPSAddress
     */
    private $USPSAddress;

    /**
     * @var USPSAddressVerify
     */
    private $USPSAddressVerify;

    public function __construct(
        Context $context,
        Registry $registry,
        Data $helper,
        Address $address,
        Response $response,
        USPSAddress $USPSAddress,
        USPSAddressVerify $USPSAddressVerify,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $helper, $address, $response, $resource, $resourceCollection, $data);

        $this->USPSAddress = $USPSAddress;
        $this->USPSAddressVerify = $USPSAddressVerify;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAddress()
    {
        $this->skipCountries();
        $this->skipRegions();

        $this->convertAddressToUSPSAddress();

        $validation = $this->getUspsVerifier();
        $validation->addAddress($this->USPSAddress);
        $validation->verify();

        $this->validateResponse($validation);
        $this->parseResponse($validation);
    }

    private function convertAddressToUSPSAddress()
    {
        $addressForCheck = $this->getAddressForValidation();

        $street = $addressForCheck->getStreet();
        $apt = "";
        $zip4 = $addressForCheck->getZip4();
        $zip5 = $addressForCheck->getZip5();
        $state = $addressForCheck->getRegionCode();
        $city = $addressForCheck->getCity();

        $this->USPSAddress->setApt($apt)
            ->setAddress($street)
            ->setCity($city)
            ->setState($state)
            ->setZip5($zip5)
            ->setZip4($zip4);

        $company = $addressForCheck->getCompany();
        if (!empty($company)) {
            $this->USPSAddress->setFirmName($company);
        }

        return $this->USPSAddress;
    }

    private function skipRegions()
    {
        $regionName = $this->getAddressForValidation()->getRegion();

        $stateForSkip = ['virgin islands', 'puerto rico', 'guam'];
        $reg = strtolower($regionName);
        if (in_array($reg, $stateForSkip)) {
            throw new LocalizedException(__("Skipped region <{$regionName}>."));
        }
    }

    private function skipCountries()
    {
        $countryId = $this->getAddressForValidation()->getCountryId();
        $countryId = strtolower($countryId);
        if ($countryId != 'us') {
            throw new LocalizedException(__("Only addresses from USA can be checked"));
        }
    }

    private function getUspsVerifier()
    {
        $testMode = $this->helper->getUspsTestMode();
        $key = $this->helper->getUspsAccountId();

        if (empty($key)) {
            throw new LocalizedException(__("Empty USPS Account ID."));
        }

        $this->USPSAddressVerify->setUsername($key);
        $this->USPSAddressVerify->setTestMode($testMode);

        return $this->USPSAddressVerify;
    }

    private function validateResponse($verify)
    {
        if (!$verify->isSuccess()) {
            $errorCode = $verify->getErrorCode();
            $errorMessage = $verify->getErrorMessage();
            throw new LocalizedException(__('Error [' . strtolower($errorCode) . ']: ' . $errorMessage));
        }

        $response = $verify->getArrayResponse();
        if (!isset($response['AddressValidateResponse']) || !isset($response['AddressValidateResponse']['Address'])) {
            throw new LocalizedException(__("Incorrect response from USPS API."));
        }
    }

    private function parseResponse($verify)
    {
        $response = $verify->getArrayResponse();

        $candidate = $response['AddressValidateResponse']['Address'];
        $validCandidate = $this->parseUspsCandidate($candidate);

        $this->addSuggestedAddress($validCandidate);
        if ($this->response->getIsValid()) {
            return;
        }
    }

    private function parseUspsCandidate($candidate)
    {
        if (!isset($candidate['Address2']) || empty($candidate['Address2']) ||
            !isset($candidate['City']) || empty($candidate['City']) ||
            !isset($candidate['State']) || empty($candidate['State']) ||
            !isset($candidate['Zip5']) || empty($candidate['Zip5'])
        ) {
            return false;
        }

        $address = clone $this->getAddressForValidation();
        $address->setData([]);

        $postcode = $candidate['Zip5'];
        if (isset($candidate['Zip4']) && !empty($candidate['Zip4'])) {
            $postcode .= '-' . $candidate['Zip4'];
        }

        $street = $candidate['Address2'];
        if (isset($candidate['Address1']) && !empty($candidate['Address1'])) {
            $street .= ' ' . $candidate['Address1'];
        }

        $address->setStreet($street);
        $address->setCity($candidate['City']);
        $address->setPostcode($postcode);
        $address->setRegion('');
        $address->setCountryId('US');
        $address->setRegionCode($candidate['State']);

        return $address;
    }

    public function getEnable()
    {
        $key = $this->helper->getUspsAccountId();
        return !empty($key);
    }
}
