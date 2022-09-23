<?php

namespace IWD\AddressValidation\Model\Ups;

use Magento\Framework\Model\AbstractModel;
use IWD\AddressValidation\Model\XML\XML2Array;

/**
 * Class UpsAPI
 * @package IWD\AddressValidation\Model\Ups
 */
abstract class UpsAPI extends AbstractModel
{
    /**
     * Status code for a failed request
     *
     * @var integer
     */
    const RESPONSE_STATUS_CODE_FAIL = 0;

    /**
     * Status code for a successful request
     *
     * @var integer
     */
    const RESPONSE_STATUS_CODE_PASS = 1;

    /**
     * Access key provided by UPS
     *
     * @access protected
     * @var string
     */
    protected $access_key;

    /**
     * Developer key provided by UPS
     *
     * @access protected
     * @var string
     */
    protected $developer_key;

    /**
     * Password used to access UPS Systems
     *
     * @access protected
     * @var string
     */
    protected $password;

    /**
     * Response from the server as XML
     *
     * @access protected
     * @var \DOMDocument
     */
    protected $response;

    /**
     * Response from the server as an array
     *
     * @access protected
     * @var array
     */
    protected $response_array;

    /**
     * Root Node for the repsonse XML
     *
     * @access protected
     * @var DOMNode
     */
    protected $root_node;

    /**
     * UPS Server to send Request to
     *
     * @access protected
     * @var string
     */
    protected $server;

    /**
     * Username used to access UPS Systems
     *
     * @access protected
     * @var string
     */
    protected $username;

    /**
     * xpath object for the response XML
     *
     * @access protected
     * @var DOMXPath
     */
    protected $xpath;

    /**
     * @var
     */
    protected $results_errors;

    /**
     * {@inheritdoc}
     */
    public function setApiCredentials($credentials)
    {
        /** Set the Keys on the Object **/
        $this->access_key = $credentials['access_key'];
        $this->developer_key = $credentials['developer_key'];

        /** Set the username and password on the Object **/
        $this->password = $credentials['password'];
        $this->username = $credentials['username'];
    }

    /**
     * Builds the XML used to make the request
     *
     * If $customerContext is an array it should be in the format:
     * $customerContext = array('Element' => 'Value');
     *
     * @param null $customerContext
     * @return string $return_value request XML
     */
    public function buildRequest($customerContext = null)
    {
        // create the access request element
        $accessDom = new \DOMDocument('1.0');
        $accessElement = $accessDom->appendChild(new \DOMElement('AccessRequest'));
        $accessElement->setAttributeNode(new \DOMAttr('xml:lang', 'en-US'));

        // create the child elements
        $accessElement->appendChild(new \DOMElement('AccessLicenseNumber', $this->access_key));
        $accessElement->appendChild(new \DOMElement('UserId', $this->username));
        $accessElement->appendChild(new \DOMElement('Password', $this->password));

        return $accessDom->saveXML();
    }

    /**
     * Returns the error message(s) from the response
     *
     * @return array
     */
    public function getError()
    {
        // iterate over the error messages
        $errors = $this->xpath->query('Response/Error', $this->root_node);
        $return_value = [];
        foreach ($errors as $error) {
            $return_value[] = [
                'severity' => $this->xpath->query('ErrorSeverity', $error)->item(0)->nodeValue,
                'code' => $this->xpath->query('ErrorCode', $error)->item(0)->nodeValue,
                'description' => $this->xpath->query('ErrorDescription', $error)->item(0)->nodeValue,
                'location' => $this->xpath->query('ErrorLocation/ErrorLocationElementName', $error)->item(0)->nodeValue,
            ];
        }

        return $return_value;
    }

    /**
     * Checks to see if a repsonse is an error
     *
     * @access public
     * @return boolean
     */
    public function isError()
    {
        $status = $this->xpath->query('Response/ResponseStatusCode', $this->root_node);
        if ($status->item(0)->nodeValue == self::RESPONSE_STATUS_CODE_FAIL) {
            return true;
        }

        return false;
    }

    /**
     * Send a request to the UPS Server using xmlrpc
     * @param $request_xml
     * @return array|mixed
     */
    public function sendRequest($request_xml)
    {
        $this->response_array = [];
        $this->results_errors = [];

        $response = false;

        // init cURL
        $ch = curl_init();

        $CURL_OPTS = [
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_USERAGENT => 'ups-php',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER => false,
        ];

        $opts = $CURL_OPTS;
        $opts[CURLOPT_POSTFIELDS] = $request_xml;
        $opts[CURLOPT_URL] = $this->server;
        $opts[CURLOPT_SSL_VERIFYPEER] = 0;
        $opts[CURLOPT_SSL_VERIFYHOST] = 0;

        // set options
        curl_setopt_array($ch, $opts);

        // execute
        $curl_response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode == 200) {
            $response = $curl_response;
        }

        curl_close($ch);

        if (empty($response)) {
            return $this->response_array;
        }

        $this->response_array = XML2Array::createArray($response);

        if (isset($this->response_array['AddressValidationResponse'])) {
            $this->response_array = $this->response_array['AddressValidationResponse'];
        } else {
            $this->response_array = [];
        }

        // check if errors
        $this->checkResultsErrors();

        return $this->response_array;
    }

    public function getResponseArray()
    {
        return $this->response_array;
    }

    private function checkResultsErrors()
    {
        $this->results_errors = [];

        $errors = [];
        $response = $this->response_array;
        if (isset($response['Response'])) {
            $response = $response['Response'];
            if (!isset($response['AddressKeyFormat'])) {
                if (isset($response['Error'])) {
                    if (isset($response['Error']['ErrorDescription'])) {
                        $errors[] = $response['Error']['ErrorDescription'];
                    }
                }
            }
        }

        $this->results_errors = $errors;
    }

    public function getResultsErrors()
    {
        return $this->results_errors;
    }

    /**
     * Builds the Request element
     *
     * @access protected
     * @param \DOMElement $dom_element
     * @param string $action
     * @param string $option
     * @param string|array $customerContext
     * @return \DOMElement
     */
    protected function buildRequest_RequestElement(&$dom_element, $action, $option = null, $customerContext = null)
    {
        $request = $dom_element->appendChild(new \DOMElement('Request'));

        $transactionElement = $request->appendChild(new \DOMElement('TransactionReference'));
        $request->appendChild(new \DOMElement('RequestAction', $action));

        if (!empty($option)) {
            $request->appendChild(new \DOMElement('RequestOption', $option));
        }

        $transactionElement->appendChild(new \DOMElement('XpciVersion', '1.0'));

        if (!empty($customerContext)) {
            if (is_array($customerContext)) {
                $customerElement = $transactionElement->appendChild(new \DOMElement('CustomerContext'));

                foreach ($customerContext as $element => $value) {
                    $customerElement->appendChild(new \DOMElement($element, $value));
                }
            } else {
                $transactionElement->appendChild(new \DOMElement('CustomerContext', $customerContext));
            }
        }

        return $request;
    }

    /**
     * Returns the name of the servies response root node
     *
     * @access protected
     * @return string
     */
    abstract public function getRootNodeName();

    /**
     * @param $addressForCheck
     * @return mixed
     */
    abstract public function setAddressForCheck($addressForCheck);
}
