<?php
define('BASEURL', 'https://transact.chargelogic.net/');
define('DEBUG', 0);
define('LOCALPROXY', 0);


class ConnectStream {
    public $path;
    public $mode;
    public $options;
    public $opened_path;
    public $buffer;
    public $pos;
    public $errors;
    public function stream_open($path, $mode, $options, $opened_path) {
        $this->path = $path;
        $this->mode = $mode;
        $this->options = $options;
        $this->opened_path = $opened_path;
        $this->createBuffer($path);
        return true;
    }
    public function stream_close() {
        curl_close($this->ch);
    }
    public function stream_read($count) {
        if(strlen($this->buffer) == 0) {
            return false;
        }
        $read = substr($this->buffer,$this->pos, $count);
        $this->pos += $count;
        return $read;
    }
    public function stream_write($data) {
        if(strlen($this->buffer) == 0) {
            return false;
        }
        return true;
    }
    public function stream_eof() {
        return ($this->pos > strlen($this->buffer));
    }
    public function stream_tell() {
        return $this->pos;
    }
    public function stream_flush() {
        $this->buffer = null;
        $this->pos = null;
    }
    public function stream_stat() {
        $this->createBuffer($this->path);
        $stat = array(
            'size' => strlen($this->buffer),
        );
        return $stat;
    }
    public function url_stat($path, $flags) {
        $this->createBuffer($path);
        $stat = array(
            'size' => strlen($this->buffer),
        );
        return $stat;
    }
    private function createBuffer($path) {
        if($this->buffer) {
            return;
        }
        $this->ch = curl_init($path);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($this->ch, CURLOPT_USERPWD, ConnectSoapClient::GetUserPwd());

        if (DEBUG == 1) {
            curl_setopt($this->ch, CURLOPT_CAINFO, realpath('./cacert.crt'));
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        if (LOCALPROXY == 1) {
            curl_setopt($this->ch, CURLOPT_PROXY, "127.0.0.1:8888");
        }

        $this->buffer = trim(curl_exec($this->ch));
        $this->pos = 0;
    }
}

class ConnectSoapClient extends SoapClient {
    protected static $userpwd;
    static function GetUserPwd()
    {
        return self::$userpwd;
    }
    function __construct($URL, $options, $StoreNo, $APIKey)
    {
        self::$userpwd = $StoreNo.":".$APIKey;
        parent::__construct($URL, $options);
    }
    function __doRequest($request, $location, $action, $version, $one_way = 0) {
        $headers = array(
            'Method: POST',
            'Connection: Keep-Alive',
            'User-Agent: PHP-SOAP-CURL',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "'.$action.'"',
        );

        $location = BASEURL;
        $this->__last_request_headers = $headers;
        $ch = curl_init($location);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true );
        $request = str_replace('</ns1:', '</', str_replace('<ns1:', '<', $request));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, self::$userpwd);
        if (DEBUG == 1) {
            curl_setopt($ch, CURLOPT_CAINFO, realpath('./cacert.crt'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        if (LOCALPROXY == 1) {
            curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:8888");
        }

        $response = trim(curl_exec($ch));
        return $response;
    }

    function __getLastRequestHeaders() {
        return implode("\n", $this->__last_request_headers)."\n";
    }
}

class ConnectClient
{
    protected $actionbase;
    protected $options = array();
    protected $existed;
    function __construct() {
        if (DEBUG == 1)
        {
            ini_set('soap.wsdl_cache_enable', 0);
            ini_set('soap.wsdl_cache_ttl', 0);
        }
        $this->existed = in_array("https", stream_get_wrappers());
        if ($this->existed) {
            stream_wrapper_unregister("https");
        }
        stream_wrapper_register('https', 'ConnectStream') or die("Failed to register protocol");
        $this->actionbase = "urn:microsoft-dynamics-schemas/codeunit/EFT_API_2:";
        $this->options = array(
        'trace'         => 1,
        'exceptions'    => 0,
        'style'         => SOAP_DOCUMENT,
        'use'           => SOAP_LITERAL,
        'soap_version'  => SOAP_1_1,
        'encoding'      => 'UTF-8'
        );

    }
    function __destruct() {
        if ($this->existed) {
            stream_wrapper_restore("https");
        }
    }
    public function CheckCharge($creds, $demand, $trans, $ident, $billing, &$response)
    {
        $params = array('credential' => $creds->toArray(), 'demandDepositAccount' => $demand->toArray(), 'transaction' => $trans->toArray(), 'identification' => $ident->toArray(), 'billingAddress' => $billing->toArray(), 'response' => $response->toArray());
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->CheckCharge($params);
        $response->fromArray($result->response);
        if ($result->return_value)
        {
            return $result->return_value;
        }
        else
        {
            throw new Exception($result->faultstring);
        }

    }
    public function CheckVerify($creds, $demand, $trans, $ident, $billing, &$response)
    {
        $params = array('credential' => $creds->toArray(), 'demandDepositAccount' => $demand->toArray(), 'transaction' => $trans->toArray(), 'identification' => $ident->toArray(), 'billingAddress' => $billing->toArray(), 'response' => $response->toArray());
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->CheckVerify($params);
        $response->fromArray($result->response);
        if ($result->return_value)
        {
            return $result->return_value;
        }
        else
        {
            throw new Exception($result->faultstring);
        }
    }
    public function CreditCardAddressVerify($creds, $card, $billing, &$response)
    {
        $params = array('credential' => $creds->toArray(), 'card' => $card->toArray(), 'billingAddress' => $billing->toArray(), 'response' => $response->toArray());
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->CreditCardAddressVerify($params);
        $response->fromArray($result->response);
        if ($result->return_value)
        {
            return $result->return_value;
        }
        else
        {
            throw new Exception($result->faultstring);
        }
    }
    public function CreditCardAuthorize($creds, $card, $trans, $billing, &$response)
    {
        $params = array('credential' => $creds->toArray(), 'card' => $card->toArray(), 'transaction' => $trans->toArray(), 'billingAddress' => $billing->toArray(), 'response' => $response->toArray());
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->CreditCardAuthorize($params);
        if ($result instanceof \SoapFault) {
            throw new Exception($result->faultstring);
        }
        $response->fromArray($result->response);
        if ($result->return_value)
        {
            return $result->return_value;
        }
        else
        {
            throw new Exception($result->faultstring);
        }
    }
    public function CreditCardCredit($creds, $card, $trans, &$response)
    {
        $params = array('credential' => $creds->toArray(), 'card' => $card->toArray(), 'transaction' => $trans->toArray(), 'response' => $response->toArray());
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->CreditCardCredit($params);
        $response->fromArray($result->response);
        if ($result->return_value)
        {
            return $result->return_value;
        }
        else
        {
            throw new Exception($result->faultstring);
        }
    }
    public function CreditCardReverse($creds, $ref, &$response)
    {
        $params = array('credential' => $creds->toArray(), 'referenceTransaction' => $ref->toArray(), 'response' => $response->toArray());
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->CreditCardReverse($params);
        $response->fromArray($result->response);
        if ($result->return_value)
        {
            return $result->return_value;
        }
        else
        {
            throw new Exception($result->faultstring);
        }
    }
    public function GiftCardActivate($creds, $card, $trans, &$response)
    {
        $params = array('credential' => $creds->toArray(), 'card' => $card->toArray(), 'transaction' => $trans->toArray(), 'response' => $response->toArray());
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->GiftCardActivate($params);
        $response->fromArray($result->response);
        if ($result->return_value)
        {
            return $result->return_value;
        }
        else
        {
            throw new Exception($result->faultstring);
        }
    }
    public function GiftCardBalanceIncrease($creds, $card, $trans, &$response)
    {
        $params = array('credential' => $creds->toArray(), 'card' => $card->toArray(), 'transaction' => $trans->toArray(), 'response' => $response->toArray());
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->GiftCardBalanceIncrease($params);
        $response->fromArray($result->response);
        if ($result->return_value)
        {
            return $result->return_value;
        }
        else
        {
            throw new Exception($result->faultstring);
        }
    }
    public function GiftCardBalanceInquiry($creds, $card, &$response)
    {
        $params = array('credential' => $creds->toArray(), 'card' => $card->toArray(), 'response' => $response->toArray());
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->GiftCardBalanceInquiry($params);
        $response->fromArray($result->response);
        if ($result->return_value)
        {
            return $result->return_value;
        }
        else
        {
            throw new Exception($result->faultstring);
        }
    }
    public function GiftCardCharge($creds, $card, $trans, &$response)
    {
        $params = array('credential' => $creds->toArray(), 'card' => $card->toArray(), 'transaction' => $trans->toArray(), 'response' => $response->toArray());
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->GiftCardCharge($params);
        $response->fromArray($result->response);
        if ($result->return_value)
        {
            return $result->return_value;
        }
        else
        {
            throw new Exception($result->faultstring);
        }
    }
    public function GiftCardCredit($creds, $card, $trans, &$response)
    {
        $params = array('credential' => $creds->toArray(), 'card' => $card->toArray(), 'transaction' => $trans->toArray(), 'response' => $response->toArray());
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->GiftCardCredit($params);
        $response->fromArray($result->response);
        if ($result->return_value)
        {
            return $result->return_value;
        }
        else
        {
            throw new Exception($result->faultstring);
        }
    }
    public function GiftCardDeactivate($creds, $card, $trans, &$response)
    {
        $params = array('credential' => $creds->toArray(), 'card' => $card->toArray(), 'transaction' => $trans->toArray(), 'response' => $response->toArray());
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->GiftCardDeactivate($params);
        $response->fromArray($result->response);
        if ($result->return_value)
        {
            return $result->return_value;
        }
        else
        {
            throw new Exception($result->faultstring);
        }
    }
    public function GiftCardReverse($creds, $ref, &$response)
    {
        $params = array('credential' => $creds->toArray(), 'referenceTransaction' => $ref->toArray(), 'response' => $response->toArray());
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->GiftCardReverse($params);
        $response->fromArray($result->response);
        if ($result->return_value)
        {
            return $result->return_value;
        }
        else
        {
            throw new Exception($result->faultstring);
        }
    }
    public function SetupHostedOrder($creds, $trans, $billing, $shipping, $payment)
    {
        $params = array('credential' => $creds->toArray(), 'transaction' => $trans->toArray(), 'billingAddress' => $billing->toArray(), 'shippingAddress' => $shipping->toArray(), 'hostedPayment' => $payment->toArray());
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->SetupHostedOrder($params);
        if ($result->return_value)
        {
            return $result->return_value;
        }
        else
        {
            throw new Exception($result->faultstring);
        }
    }
    public function SetupHostedCreditCardAVS($creds, $trans, $billing, $shipping, $payment)
    {
        $params = array('credential' => $creds->toArray(), 'transaction' => $trans->toArray(), 'billingAddress' => $billing->toArray(), 'shippingAddress' => $shipping->toArray(), 'hostedPayment' => $payment->toArray());
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->SetupHostedCreditCardAVS($params);
        if ($result->return_value)
        {
            return $result->return_value;
        }
        else
        {
            throw new Exception($result->faultstring);
        }
    }
    public function SetupHostedCreditCardAuthorize($creds, $trans, $billing, $shipping, $payment)
    {
        $params = array('credential' => $creds->toArray(), 'transaction' => $trans->toArray(), 'billingAddress' => $billing->toArray(), 'shippingAddress' => $shipping->toArray(), 'hostedPayment' => $payment->toArray());
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->SetupHostedCreditCardAuthorize($params);
        if ($result->return_value)
        {
            return $result->return_value;
        }
        else
        {
            throw new Exception($result->faultstring);
        }
    }
    public function SetupHostedCreditCardCredit($creds, $trans, $billing, $shipping, $payment)
    {
        $params = array('credential' => $creds->toArray(), 'transaction' => $trans->toArray(), 'billingAddress' => $billing->toArray(), 'shippingAddress' => $shipping->toArray(), 'hostedPayment' => $payment->toArray());
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->SetupHostedCreditCardCredit($params);
        if ($result->return_value)
        {
            return $result->return_value;
        }
        else
        {
            throw new Exception($result->faultstring);
        }
    }
    public function FinalizeOrder($creds, $id)
    {
        $params = array('credential' => $creds->toArray(), 'hostedPaymentID' => $id);
        $action = $this->actionbase.__METHOD__;
        $client = new ConnectSoapClient(BASEURL, $this->options, $creds->StoreNo, $creds->APIKey);
        $result = $client->FinalizeOrder($params);
    }
}

class ConnectCredential
{
    public $StoreNo;
    public $APIKey;
    public $ApplicationNo;
    public $ApplicationVersion;
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }
    public function toArray()
    {
        return get_object_vars($this);
    }
}
class ConnectCard
{
    public $CardholderName;
    public $AccountNumber;
    public $ExpirationMonth;
    public $ExpirationYear;
    public $Token;
    public $CardVerificationValue;
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }
    public function toArray()
    {
        return get_object_vars($this);
    }
}
class ConnectExtraData
{
    public $Name;
    public $Value;
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }
    public function toArray()
    {
        return get_object_vars($this);
    }
}
class ConnectComment
{
    public $CommentLine;
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }
    public function toArray()
    {
        return get_object_vars($this);
    }
}
class ConnectLineItem
{
    public $ProductCode;
    public $Category;
    public $Description;
    public $Quantity;
    public $UnitOfMeasure;
    public $UnitPrice;
    public $LineTaxAmount;
    public $LineDiscountAmount;
    public $LineAmount;
    public $ExtraData = array();
    public function addExtraData($extradata)
    {
        array_push($this->ExtraData, $extradata->toArray());
    }
    public function addExtraDataArray($arrextradata)
    {
        foreach ($arrextradata as $extradata)
        {
            $this->addExtraData($extradata);
        }
    }
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }
    public function toArray()
    {
        return get_object_vars($this);
    }
}
class ConnectTransaction
{
    public $Currency;
    public $Amount;
    public $FreightAmount;
    public $TaxAmount;
    public $PurchaseOrderNumber;
    public $PurchaseOrderDate;
    public $ExternalReferenceNumber;
    public $AuthorizationNumber;
    public $ConfirmationID;
    public $LineItem = array();
    public function addLineItem($lineitem)
    {
        array_push($this->LineItem, $lineitem->toArray());
    }
    public function addLineItemArray($arrlineitem)
    {
        foreach ($arrlineitem as $lineitem)
        {
            $this->addLineItem($lineitem);
        }
    }
    public function __construct()
    {
        $this->ConfirmationID = self::getGUID();
    }
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }
    public function toArray()
    {
        return get_object_vars($this);
    }
    static function getGUID(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        } else {
            mt_srand((double)microtime()*10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = //chr(123)// "{"
                substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
                //.chr(125);// "}"
            return $uuid;
        }
    }
}
class ConnectAddress
{
    public $Name;
    public $StreetAddress;
    public $StreetAddress2;
    public $City;
    public $State;
    public $PostCode;
    public $Country;
    public $PhoneNumber;
    public $Email;
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }
    public function toArray()
    {
        return get_object_vars($this);
    }
}
class ConnectDemandDepositAccount
{
    public $RoutingNumber;
    public $AccountNumber;
    public $Token;
    public $CheckNumber;
    public $CheckType;
    public $AccountType;
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }
    public function toArray()
    {
        return get_object_vars($this);
    }
}
class ConnectIdentification
{
    public $BirthDate;
    public $IdentificationNumber;
    public $IdentificationState;
    public $TaxIDNumber;
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }
    public function toArray()
    {
        return get_object_vars($this);
    }
}
class ConnectReferenceTransaction
{
    public $OriginalReferenceNumber;
    public $Amount;
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }
    public function toArray()
    {
        return get_object_vars($this);
    }
}
class ConnectHostedPayment
{
    public $RequireCVV;
    public $ReturnURL;
    public $Language;
    public $LogoURL;
    public $ShippingAgent;
    public $ShippingAgentDescription;
    public $ShippingAgentService;
    public $ShippingAgentServiceDescription;
    public $ConfirmationID;
    public $PageBackgroundColor;
    public $ButtonBackgroundColor;
    public $HeaderFontColor;
    public $FieldLabelFontColor;
    public $BorderColor;
    public $ErrorColor;
    public $Embedded;
    public $MerchantResourceURL;
    public $Salesperson;
    public $WebPaymentGateway;
    public $WebPaymentTransactionID;
    public $WebPaymentAmount;
    public $ExtraDataField = array();
	public $Comment = array();
    public function addExtraData($extradata)
    {
        array_push($this->ExtraDataField, $extradata->toArray());
    }
    public function addExtraDataArray($arrextradata)
    {
        foreach ($arrextradata as $extradata)
        {
            $this->addExtraData($extradata);
        }
    }
    public function addComment($comment)
    {
        array_push($this->Comment, $comment->toArray());
    }
    public function addCommentArray($arrcomment)
    {
        foreach ($arrcomment as $comment)
        {
            $this->addComment($comment);
        }
    }
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }
    public function toArray()
    {
        return get_object_vars($this);
    }
}
class ConnectResponse
{
    public $ResponseCode;
    public $HostResponseCode;
    public $Message;
    public $ApprovalNumber;
    public $ApprovedAmount;
    public $BalanceAmount;
    public $TransactionDate;
    public $TransactionTime;
    public $TransactionStatus;
    public $AddressVerificationAlert;
    public $CardVerificationValueAlert;
    public $MaskedAccountNumber;
    public $Token;
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }
    public function toArray()
    {
        return get_object_vars($this);
    }
    public function fromArray($arr)
    {
        foreach ($arr as $key => $value)
        {
            $this->$key = $value;
        }
    }
}


?>
