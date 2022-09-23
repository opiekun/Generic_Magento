<?php

namespace IWD\AddressValidation\Model\Ups\UpsAPI;

use IWD\AddressValidation\Model\Ups\UpsAPI;

/**
 * Class USStreetLevelValidation
 * @package IWD\AddressValidation\Model\Ups\UpsAPI
 */
class USStreetLevelValidation extends UpsAPI
{
    /**
     * Node name for the root node
     *
     * @var string
     */
    const NODE_NAME_ROOT_NODE = '';

    /**
     * Shipping address that we are to validate
     *
     * @access protected
     * @param array
     */
    private $address;

    /**
     * {@inheritdoc}
     */
    public function setApiCredentials($credentials)
    {
        parent::setApiCredentials($credentials);
        $this->server = $credentials['ups_street_level_api'] . '/ups.app/xml/XAV';
    }

    /**
     * {@inheritdoc}
     */
    public function buildRequest($customer_context = null)
    {
        /** create DOMDocument objects **/
        $addressDom = new \DOMDocument ('1.0');

        /** create the AddressValidationRequest element **/
        $addressElement = $addressDom->appendChild(new \DOMElement('AddressValidationRequest'));
        $addressElement->setAttributeNode(new \DOMAttr('xml:lang', 'en-US'));

        // create the child elements
        $this->buildRequest_RequestElement($addressElement, 'XAV', 3, $customer_context);

        $addressElement->appendChild(new \DOMElement('MaximumListSize', 3));

        $addressElement = $addressElement->appendChild(new \DOMElement('AddressKeyFormat'));

        (!empty($this->address['street'])) ? $addressElement->appendChild(new \DOMElement('AddressLine', $this->address['street'])) : false;
        (!empty($this->address['city'])) ? $addressElement->appendChild(new \DOMElement('PoliticalDivision2', $this->address['city'])) : false;
        (!empty($this->address['region_code'])) ? $addressElement->appendChild(new \DOMElement('PoliticalDivision1', $this->address['region_code'])) : false;
        (!empty($this->address['zip5'])) ? $addressElement->appendChild(new \DOMElement('PostcodePrimaryLow', $this->address['zip5'])) : false;
        (!empty($this->address['zip4'])) ? $addressElement->appendChild(new \DOMElement('PostcodeExtendedLow', $this->address['zip4'])) : false;
        (!empty($this->address['country_id'])) ? $addressElement->appendChild(new \DOMElement('CountryCode', $this->address['country_id'])) : false;

        return parent::buildRequest() . $addressDom->saveXML();
    }

    /**
     * Returns the type of match(s)
     *
     * @access public
     * @return string $return_value whether or not a full or partial match was
     * found
     */
    public function getMatchType()
    {
        if (!isset($this->response_array['AddressClassification'])) {
            return 'Unknown';
        }

        $matchArray = $this->response_array['AddressClassification'];

        if (isset($matchArray ['Code'])) {
            if ($matchArray ['Code'] == 0) {
                return 'Unknown';
            }

            if ($matchArray ['Code'] == 1) {
                return 'Commercial';
            }

            if ($matchArray ['Code'] == 2) {
                return 'Residential';
            }
        }

        return 'Unknown';
    }

    /**
     * {@inheritdoc}
     */
    public function getRootNodeName()
    {
        return self::NODE_NAME_ROOT_NODE;
    }

    /**
     * {@inheritdoc}
     */
    public function setAddressForCheck($address)
    {
        /// check for zip4
        if (isset($address['zip_code'])) {
            $pattern = "/([^0-9])/";
            $zip = trim(preg_replace($pattern, '', $address['zip_code']));
            if (strlen($zip) > 5) {
                $zip5 = substr($zip, 0, 5);
                $zip4 = substr($zip, 5);

                if (strlen($zip5) == 5 && strlen($zip4) == 4) {
                    $address['zip_code'] = $zip5;
                    $address['zip_code4'] = $zip4;
                }
            }
        }

        $this->address = $address;
    }

    /**
     * Checks a match type to see if it is valid
     *
     * @access protected
     * @param string $matchType match type to validate
     * @return bool whether or not the match type is valid
     */
    private function validateMatchType($matchType)
    {
        // declare the valid match types
        $validMatchTypes = ['Unknown', 'Commercial', 'Residential'];

        return in_array($matchType, $validMatchTypes);
    }
}
