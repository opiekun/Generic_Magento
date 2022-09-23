<?php

namespace IWD\AddressValidation\Model\Ups;

use IWD\AddressValidation\Model\Ups\UpsAPI\USStreetLevelValidation;
use IWD\AddressValidation\Model\AbstractValidation;
use IWD\AddressValidation\Model\Validation\Address;
use IWD\AddressValidation\Model\Validation\Response;
use IWD\AddressValidation\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Validation
 * @package IWD\AddressValidation\Model\Ups
 */
class Validation extends AbstractValidation
{
    /**
     * @var USStreetLevelValidation
     */
    private $validator;

    /**
     * Validation constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Data $helper
     * @param Address $address
     * @param Response $response
     * @param USStreetLevelValidation $validator
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
        USStreetLevelValidation $validator,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $helper, $address, $response, $resource, $resourceCollection, $data);

        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAddress()
    {
        $this->skipCountries();
        $this->skipRegions();

        $addressForCheck = $this->getAddressForValidation()->getData();
        $addressForCheck['zip5'] = $this->getAddressForValidation()->getZip5();
        $addressForCheck['zip4'] = $this->getAddressForValidation()->getZip4();
        $addressForCheck['region_code'] = $this->getAddressForValidation()->getRegionCode();

        $this->validator->setAddressForCheck($addressForCheck);
        $this->setupConfigUPS();

        $xml = $this->validator->buildRequest();

        $response = $this->validator->sendRequest($xml);

        $this->validateResponse($response, $this->validator);

        $this->getUpsCandidates($response);
    }

    /**
     * @param $response
     * @param $validation
     * @throws LocalizedException
     */
    private function validateResponse($response, $validation)
    {
        if (!isset($response['Response'])) {
            throw new LocalizedException(__("Incorrect response from UPS API."));
        }

        if (!isset($response['AddressKeyFormat'])) {
            $errors = $validation->getResultsErrors();
            if (!empty($errors)) {
                $err = implode('; ', $errors);
                throw new LocalizedException(__($err));
            }
        }
    }

    /**
     * @throws LocalizedException
     */
    private function setupConfigUPS()
    {
        $test_mode = $this->helper->getUpsTestMode();
        $login = $this->helper->getUpsLogin();
        $pass = $this->helper->getUpsPassword();
        $key = $this->helper->getUpsAccessKey();

        if (empty($key) || empty($login) || empty($pass)) {
            throw new LocalizedException(__("Empty UPS credentials."));
        }

        $credentials = [];
        $credentials['access_key'] = $key;
        $credentials['developer_key'] = '';

        if ($test_mode) {
            $credentials['server'] = 'https://wwwcie.ups.com';
            $credentials['ups_street_level_api'] = 'https://wwwcie.ups.com';
            // in other DOCS test server should be  https://wwwcie.ups.com/webservices/XAV
        } else {
            $credentials['server'] = 'https://www.ups.com';
            $credentials['ups_street_level_api'] = 'https://onlinetools.ups.com';
            // in other DOCS live server should be  https://onlinetools.ups.com/webservices/XAV
        }

        $credentials['username'] = $login;
        $credentials['password'] = $pass;

        $this->validator->setApiCredentials($credentials);
    }

    /**
     * @throws LocalizedException
     */
    private function skipRegions()
    {
        $regionName = $this->getAddressForValidation()->getRegion();

        $stateForSkip = ['virgin islands', 'puerto rico', 'guam'];
        $reg = strtolower($regionName);
        if (in_array($reg, $stateForSkip)) {
            throw new LocalizedException(__("Skipped region <{$regionName}>."));
        }
    }

    /**
     * @throws LocalizedException
     */
    private function skipCountries()
    {
        $countryId = $this->getAddressForValidation()->getCountryId();
        $countryId = strtolower($countryId);
        if ($countryId != 'us') {
            throw new LocalizedException(__("Only addresses from USA can be checked"));
        }
    }

    /**
     * @param $response
     */
    private function getUpsCandidates($response)
    {
        if (!isset($response['AddressKeyFormat'])) {
            $this->response->setIsValid(false);
            return;
        }

        $addresses_array = $response['AddressKeyFormat'];
        if (isset($addresses_array['AddressClassification'])) {
            $validCandidate = $this->parseUpsCandidate($addresses_array);
            $this->addSuggestedAddress($validCandidate);
        } else {
            // we have list of addresses
            foreach ($addresses_array as $candidate) {
                $validCandidate = $this->parseUpsCandidate($candidate);
                $this->addSuggestedAddress($validCandidate);
                if ($this->response->getIsValid()) {
                    break;
                }
            }
        }
    }

    /**
     * @param $candidate
     * @return bool|Address
     */
    private function parseUpsCandidate($candidate)
    {
        if (!isset($candidate['PostcodePrimaryLow']) ||
            !isset($candidate['AddressLine']) ||
            !isset($candidate['PoliticalDivision2']) ||
            !isset($candidate['PoliticalDivision1']) ||
            !isset($candidate['PostcodePrimaryLow'])
        ) {
            return false;
        }

        $address = clone $this->getAddressForValidation();
        $address->setData([]);
        $address->setStreet($candidate['AddressLine']);
        $address->setCity($candidate['PoliticalDivision2']);
        $address->setRegion('');
        $address->setCountryId('US');
        $address->setRegionCode($candidate['PoliticalDivision1']);

        $postcode = $candidate['PostcodePrimaryLow'];
        if (isset($candidate['PostcodeExtendedLow']) && !empty($candidate['PostcodeExtendedLow'])) {
            $postcode .= '-' . $candidate['PostcodeExtendedLow'];
        }
        $address->setPostcode($postcode);

        if ($this->helper->getUpsShowAddressType()) {
            $classCode = '';
            if (isset($candidate['AddressClassification']['Code'])) {
                $classCode = $candidate['AddressClassification']['Code'];
                $address->setClassCode($classCode);
            }
            if (isset($candidate['AddressClassification']['Description'])) {
                $description = $candidate['AddressClassification']['Description'];
                if (empty($description) && !empty($classCode)) {
                    $description = $classCode == 1 ? 'Commercial' : ( $classCode == 2 ? 'Residential' : "");
                }
                if ($description != 'Unknown') {
                    $address->setClassDescription($description);
                }
            }
        }

        return $address;
    }

    /**
     * @return bool
     */
    public function getEnable()
    {
        $login = $this->helper->getUpsLogin();
        $pass = $this->helper->getUpsPassword();
        $key = $this->helper->getUpsAccessKey();

        return !(empty($key) || empty($login) || empty($pass));
    }
}
