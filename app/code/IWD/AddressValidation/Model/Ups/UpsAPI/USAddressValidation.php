<?php
/**
 * Handles the validation of US Shipping Addresses
 *
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 */

namespace IWD\AddressValidation\Model\Ups\UpsAPI;

use IWD\AddressValidation\Model\Ups\UpsAPI;

class USAddressValidation extends UpsAPI
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
        $this->server = $credentials['server'] . '/ups.app/xml/AV';
    }

    /**
     * Gets the current city on the object
     *
     * @access public
     * @return string the current city
     */
    public function getCity()
    {
        return $this->address['city'];
    }

    /**
     * @param $city
     * @return $this
     */
    public function setCity($city)
    {
        $this->address ['city'] = $city;
        return $this;
    }

    /**
     * Gets the current full address on the object
     *
     * @access public
     * @return array the current address
     */
    public function getFullAddress()
    {
        return $this->address;
    }

    /**
     * @param $address
     * @return $this
     */
    public function setFullAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Gets the current state on the object
     *
     * @access public
     * @return string the current state
     */
    public function getState()
    {
        return $this->address ['state'];
    }

    /**
     * @param $state
     * @return $this
     */
    public function setState($state)
    {
        $this->address ['state'] = $state;
        return $this;
    }

    /**
     * Gets the current zip code on the object
     *
     * @access public
     * @return integer the curret zip code
     */
    public function getZipCode()
    {
        return $this->address ['zip_code'];
    }

    /**
     * @param $zip_code
     * @return $this
     */
    public function setZipCode($zip_code)
    {
        $this->address ['zip_code'] = $zip_code;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function buildRequest($customer_context = null)
    {
        /** create DOMDocument objects **/
        $addressDom = new \DOMDocument('1.0');

        /** create the AddressValidationRequest element **/
        $addressElement = $addressDom->appendChild(new \DOMElement('AddressValidationRequest'));
        $addressElement->setAttributeNode(new \DOMAttr('xml:lang', 'en-US'));

        // create the child elements
        $request_element = $this->buildRequest_RequestElement($addressElement, 'AV', null, $customer_context);
        $addressElement = $addressElement->appendChild(new \DOMElement('Address'));

        /** create the children of the Address Element **/
        // check if a city was entered
        (!empty($this->address['city'])) ? $addressElement->appendChild(new \DOMElement('City', $this->address['city'])) : false;
        (!empty($this->address['state'])) ? $addressElement->appendChild(new \DOMElement('StateProvinceCode', $this->address['state'])) : false;
        (!empty($this->address['zip_code'])) ? $addressElement->appendChild(new \DOMElement('PostalCode', $this->address['zip_code'])) : false;

        return parent::buildRequest() . $addressDom->saveXML();
    }

    /**
     * Gets any matches from the response including their rank and quality
     *
     * @access public
     * @param string $matchType type of match returned by getMatchType()
     * @return array $returnValue array of matches
     */
    public function getMatches($matchType = null)
    {
        $returnValue = [];

        if (empty($matchType)) {
            $matchType = $this->getMatchType();
        }

        if ($matchType == 'None') {
            return $returnValue;
        }

        // check if we only have one match
        $match_array = $this->response_array ['AddressValidationResult'];
        if (!isset($match_array[0])) {
            $returnValue[0] = [
                'quality' => $match_array ['Quality'],
                'address' => [
                    'city' => $match_array ['Address'] ['City'],
                    'state' => $match_array ['Address'] ['StateProvinceCode']
                ],
                'zip_code_low' => $match_array ['PostalCodeLowEnd'],
                'zip_code_high' => $match_array ['PostalCodeHighEnd']
            ];
        } else {
            foreach ($match_array as $current_match) {
                $returnValue[] = [
                    'quality' => $current_match ['Quality'],
                    'address' => [
                        'city' => $current_match ['Address'] ['City'],
                        'state' => $current_match ['Address'] ['StateProvinceCode']
                    ],
                    'zip_code_low' => $current_match ['PostalCodeLowEnd'],
                    'zip_code_high' => $current_match ['PostalCodeHighEnd']
                ];
            }
        }

        return $returnValue;
    }

    /**
     * Returns the type of match(s)
     *
     * @access public
     * @return string $returnValue whether or not a full or partial match was
     * found
     */
    public function getMatchType()
    {
        if (!isset($this->response_array['AddressValidationResult'])) {
            return 'None';
        }

        $match_array = $this->response_array['AddressValidationResult'];
        switch ($match_array) {
            case isset($match_array['Quality']) && $match_array['Quality'] == '1.0':
                $returnValue = 'Exact';
                break;

            case isset($match_array['Quality']):
                $returnValue = 'Partial';
                break;

            case count($match_array) > 1:
                // iterate over the results to see if we have an exact match
                foreach ($match_array as $result) {
                    if ($result ['Quality'] == '1.0') {
                        $returnValue = 'Multiple With Exact';
                        break (2);
                    }
                }

                $returnValue = 'Multiple Partial';
                break;

            default:
                $returnValue = false;
                break;
        }

        return $returnValue;
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
    public function setAddressForCheck($addressForCheck)
    {
        $this->address = $addressForCheck;
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
        $validMatchTypes = ['Exact', 'None', 'Partial', 'Multiple With Exact', 'Multiple Partial'];

        return in_array($matchType, $validMatchTypes);
    }
}
