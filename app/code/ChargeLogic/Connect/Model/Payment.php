<?php

namespace ChargeLogic\Connect\Model;


class Payment extends \Magento\Payment\Model\Method\Cc
{
    const CODE = 'chargelogic_connect';

    protected $_code = self::CODE;

    protected $_isGateway                   = true;
    protected $_canCapture                  = true;
    protected $_canCapturePartial           = true;
    protected $_canRefund                   = true;
    protected $_canRefundInvoicePartial     = true;

    protected $_connectApi = false;

    protected $_countryFactory;
    protected $_supportedCurrencyCodes;

    protected $_encryptor;

    protected $_debugReplacePrivateDataKeys = ['number', 'exp_month', 'exp_year', 'cvc'];

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        //ChargeLogic\Connect $connect,
        array $data = array()
    ) {
        $this->_encryptor = $encryptor;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $moduleList,
            $localeDate,
            null,
            null,
            $data
        );



        $this->_countryFactory = $countryFactory;

        //$this->_supportedCurrencyCodes = $this->getConfigData('allowedcurrency');

        /*$this->_connectApi = $connect;
        $this->_connectApi->setApiKey(
            $this->getConfigData('api_key')
        );*/


    }

    public function decrypt($data)
    {
        return $this->_encryptor->decrypt($data);
    }

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {


        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();

        /** @var \Magento\Sales\Model\Order\Address $billing */
        $billing_address = $order->getBillingAddress();
        $shipping_address = $order->getShippingAddress();

        require_once('ChargeLogicConnect.php');

        $connect = new \ConnectClient();
        $creds = new \ConnectCredential();


        if ($this->getConfigData('test_mode') == "1")
        {
            $creds->StoreNo = $this->getConfigData('store_no_test');
            $creds->APIKey = $this->decrypt($this->getConfigData('api_key_test'));

            $creds->ApplicationNo = "WMCATA";
            $creds->ApplicationVersion = "2.0.4";
        }
        else
        {
           $creds->StoreNo = $this->getConfigData('store_no');
            $creds->APIKey = $this->decrypt($this->getConfigData('api_key'));

            $creds->ApplicationNo = "WMCATA";
            $creds->ApplicationVersion = "2.0.4";
        }



        $trans = new \ConnectTransaction();
        $trans->Currency = $order->getBaseCurrencyCode();
        $trans->Amount = $amount;
        $trans->FreightAmount = $order->getShippingAmount();
        $trans->ExternalReferenceNumber = $order->getIncrementId();

        $lineItems = array();
		$items = $order->getAllVisibleItems();
		$lineCount = 0;
		foreach ($items as $item)
		{
			array_push($lineItems, new \ConnectLineItem());
			$lineItems[$lineCount]->Description = substr($item->getName(), 0, 50);
			$lineItems[$lineCount]->Category = "";
			$lineItems[$lineCount]->UnitPrice = $item->getPrice();
			$lineItems[$lineCount]->ProductCode = substr($item->getSku(), 0, 20);
			$item->getProductId();
			$lineItems[$lineCount]->Quantity = $item->getQtyOrdered();
			$lineItems[$lineCount]->UnitOfMeasure = "PCE";
			$lineItems[$lineCount]->LineAmount = ($item->getPrice() * $item->getQtyOrdered()) - $item->getDiscountAmount();
			$lineItems[$lineCount]->LineDiscountAmount = $item->getDiscountAmount();

			$lineCount++;
		}
		$trans->addLineItemArray($lineItems);




        $billing = new \ConnectAddress();
        $billing->Name = substr($billing_address->getName(), 0, 50);
        $billing->StreetAddress = substr($billing_address->getStreetLine(1), 0, 50);
        $billing->StreetAddress2 = substr($billing_address->getStreetLine(2), 0, 50);
        $billing->City = substr($billing_address->getCity(), 0, 30);
        $billing->State = substr($billing_address->getRegion(), 0, 30);
        $billing->PostCode = substr($billing_address->getPostcode(), 0, 30);

        $billing_country_iso = $this->getCountryInfo($billing_address->getCountryId());
        $billing->Country = $billing_country_iso[2];
        $billing->PhoneNumber = substr($billing_address->getTelephone(), 0, 30);
        $billing->Email = substr($order->getCustomerEmail(), 0, 80);


        $shipping = new \ConnectAddress();
        $shipping->Name = substr($shipping_address->getName(), 0, 50);
        $shipping->StreetAddress = substr($shipping_address->getStreetLine(1), 0, 50);
        $shipping->StreetAddress2 = substr($shipping_address->getStreetLine(2), 0, 50);
        $shipping->City = substr($shipping_address->getCity(), 0, 30);
        $shipping->State = substr($shipping_address->getRegion(), 0, 30);
        $shipping->PostCode = substr($shipping_address->getPostcode(), 0, 30);

        $shipping_country_iso = $this->getCountryInfo($shipping_address->getCountryId());
        $shipping->Country = $shipping_country_iso[2];
        $shipping->PhoneNumber = substr($shipping_address->getTelephone(), 0, 30);
        $shipping->Email = substr($order->getCustomerEmail(), 0, 80);



        $hostedpayment = new \ConnectHostedPayment();
		$hostedpayment->RequireCVV = "No";
		$shippingmethod = explode("_", $order->getShippingMethod());
		if (count($shippingmethod) > 0)
		{
		$hostedpayment->ShippingAgent = substr($shippingmethod[0], 0, 10);
		}
		if (count($shippingmethod) > 1)
		{
		$hostedpayment->ShippingAgentService = substr($shippingmethod[1], 0, 10);
		}
		$hostedpayment->Language = "ENG"; // Use a standard ISO language code
		$hostedpayment->ConfirmationID = $trans->ConfirmationID; // Use the Confirmation ID from the Transaction



        $card = new \ConnectCard();
    	$card->AccountNumber = $payment->getCcNumber();
    	$card->ExpirationMonth = sprintf('%02d',$payment->getCcExpMonth());
    	$card->ExpirationYear = substr($payment->getCcExpYear(),-2,2);
    	$card->CardVerificationValue = $payment->getCcCid();


        $response = new \ConnectResponse();

        try
    	{
    		$return_value = $connect->CreditCardAuthorize($creds, $card, $trans, $billing, $response);

            if ($response->TransactionStatus == "Approved")
    		{
    			if($this->getConfigData('send_order_info') == "1")
                {
                    $connect->FinalizeOrder($creds, $connect->SetupHostedOrder($creds, $trans, $billing, $shipping, $hostedpayment));
                }

                $payment
                    ->setTransactionId($return_value)
                        ->setIsTransactionClosed(0);

                return $this;
    		}
            else
            {
    			 throw new \Magento\Framework\Exception\LocalizedException(__('Payment capturing error.'));
    		}
    	}
        catch(Exception $e)
        {

            throw new \Magento\Framework\Exception\LocalizedException(__('Payment capturing error.'));

    	}

    }


    /*
    $requestData = [
                'amount'        => $amount * 100,
                'currency'      => strtolower($order->getBaseCurrencyCode()),
                'description'   => sprintf('#%s, %s', $order->getIncrementId(), $order->getCustomerEmail()),
                'card'          => [
                    'number'            => $payment->getCcNumber(),
                    'exp_month'         => sprintf('%02d',$payment->getCcExpMonth()),
                    'exp_year'          => $payment->getCcExpYear(),
                    'cvc'               => $payment->getCcCid(),
                    'name'              => $billing->getName(),
                    'address_line1'     => $billing->getStreetLine(1),
                    'address_line2'     => $billing->getStreetLine(2),
                    'address_city'      => $billing->getCity(),
                    'address_zip'       => $billing->getPostcode(),
                    'address_state'     => $billing->getRegion(),
                    'address_country'   => $billing->getCountryId(),
                    // To get full localized country name, use this instead:
                    // 'address_country'   => $this->_countryFactory->create()->loadByCode($billing->getCountryId())->getName(),
                ]
            ];

    */


    public function getCountryInfo($two_digit_code)
    {
        $countries = array(
            "AF" => array("AFGHANISTAN", "AF", "AFG", "004"),
            "AL" => array("ALBANIA", "AL", "ALB", "008"),
            "DZ" => array("ALGERIA", "DZ", "DZA", "012"),
            "AS" => array("AMERICAN SAMOA", "AS", "ASM", "016"),
            "AD" => array("ANDORRA", "AD", "AND", "020"),
            "AO" => array("ANGOLA", "AO", "AGO", "024"),
            "AI" => array("ANGUILLA", "AI", "AIA", "660"),
            "AQ" => array("ANTARCTICA", "AQ", "ATA", "010"),
            "AG" => array("ANTIGUA AND BARBUDA", "AG", "ATG", "028"),
            "AR" => array("ARGENTINA", "AR", "ARG", "032"),
            "AM" => array("ARMENIA", "AM", "ARM", "051"),
            "AW" => array("ARUBA", "AW", "ABW", "533"),
            "AU" => array("AUSTRALIA", "AU", "AUS", "036"),
            "AT" => array("AUSTRIA", "AT", "AUT", "040"),
            "AZ" => array("AZERBAIJAN", "AZ", "AZE", "031"),
            "BS" => array("BAHAMAS", "BS", "BHS", "044"),
            "BH" => array("BAHRAIN", "BH", "BHR", "048"),
            "BD" => array("BANGLADESH", "BD", "BGD", "050"),
            "BB" => array("BARBADOS", "BB", "BRB", "052"),
            "BY" => array("BELARUS", "BY", "BLR", "112"),
            "BE" => array("BELGIUM", "BE", "BEL", "056"),
            "BZ" => array("BELIZE", "BZ", "BLZ", "084"),
            "BJ" => array("BENIN", "BJ", "BEN", "204"),
            "BM" => array("BERMUDA", "BM", "BMU", "060"),
            "BT" => array("BHUTAN", "BT", "BTN", "064"),
            "BO" => array("BOLIVIA", "BO", "BOL", "068"),
            "BA" => array("BOSNIA AND HERZEGOVINA", "BA", "BIH", "070"),
            "BW" => array("BOTSWANA", "BW", "BWA", "072"),
            "BV" => array("BOUVET ISLAND", "BV", "BVT", "074"),
            "BR" => array("BRAZIL", "BR", "BRA", "076"),
            "IO" => array("BRITISH INDIAN OCEAN TERRITORY", "IO", "IOT", "086"),
            "BN" => array("BRUNEI DARUSSALAM", "BN", "BRN", "096"),
            "BG" => array("BULGARIA", "BG", "BGR", "100"),
            "BF" => array("BURKINA FASO", "BF", "BFA", "854"),
            "BI" => array("BURUNDI", "BI", "BDI", "108"),
            "KH" => array("CAMBODIA", "KH", "KHM", "116"),
            "CM" => array("CAMEROON", "CM", "CMR", "120"),
            "CA" => array("CANADA", "CA", "CAN", "124"),
            "CV" => array("CAPE VERDE", "CV", "CPV", "132"),
            "KY" => array("CAYMAN ISLANDS", "KY", "CYM", "136"),
            "CF" => array("CENTRAL AFRICAN REPUBLIC", "CF", "CAF", "140"),
            "TD" => array("CHAD", "TD", "TCD", "148"),
            "CL" => array("CHILE", "CL", "CHL", "152"),
            "CN" => array("CHINA", "CN", "CHN", "156"),
            "CX" => array("CHRISTMAS ISLAND", "CX", "CXR", "162"),
            "CC" => array("COCOS (KEELING) ISLANDS", "CC", "CCK", "166"),
            "CO" => array("COLOMBIA", "CO", "COL", "170"),
            "KM" => array("COMOROS", "KM", "COM", "174"),
            "CG" => array("CONGO", "CG", "COG", "178"),
            "CK" => array("COOK ISLANDS", "CK", "COK", "184"),
            "CR" => array("COSTA RICA", "CR", "CRI", "188"),
            "CI" => array("COTE D'IVOIRE", "CI", "CIV", "384"),
            "HR" => array("CROATIA (local name: Hrvatska)", "HR", "HRV", "191"),
            "CU" => array("CUBA", "CU", "CUB", "192"),
            "CY" => array("CYPRUS", "CY", "CYP", "196"),
            "CZ" => array("CZECH REPUBLIC", "CZ", "CZE", "203"),
            "DK" => array("DENMARK", "DK", "DNK", "208"),
            "DJ" => array("DJIBOUTI", "DJ", "DJI", "262"),
            "DM" => array("DOMINICA", "DM", "DMA", "212"),
            "DO" => array("DOMINICAN REPUBLIC", "DO", "DOM", "214"),
            "TL" => array("EAST TIMOR", "TL", "TLS", "626"),
            "EC" => array("ECUADOR", "EC", "ECU", "218"),
            "EG" => array("EGYPT", "EG", "EGY", "818"),
            "SV" => array("EL SALVADOR", "SV", "SLV", "222"),
            "GQ" => array("EQUATORIAL GUINEA", "GQ", "GNQ", "226"),
            "ER" => array("ERITREA", "ER", "ERI", "232"),
            "EE" => array("ESTONIA", "EE", "EST", "233"),
            "ET" => array("ETHIOPIA", "ET", "ETH", "210"),
            "FK" => array("FALKLAND ISLANDS (MALVINAS)", "FK", "FLK", "238"),
            "FO" => array("FAROE ISLANDS", "FO", "FRO", "234"),
            "FJ" => array("FIJI", "FJ", "FJI", "242"),
            "FI" => array("FINLAND", "FI", "FIN", "246"),
            "FR" => array("FRANCE", "FR", "FRA", "250"),
            "FX" => array("FRANCE, METROPOLITAN", "FX", "FXX", "249"),
            "GF" => array("FRENCH GUIANA", "GF", "GUF", "254"),
            "PF" => array("FRENCH POLYNESIA", "PF", "PYF", "258"),
            "TF" => array("FRENCH SOUTHERN TERRITORIES", "TF", "ATF", "260"),
            "GA" => array("GABON", "GA", "GAB", "266"),
            "GM" => array("GAMBIA", "GM", "GMB", "270"),
            "GE" => array("GEORGIA", "GE", "GEO", "268"),
            "DE" => array("GERMANY", "DE", "DEU", "276"),
            "GH" => array("GHANA", "GH", "GHA", "288"),
            "GI" => array("GIBRALTAR", "GI", "GIB", "292"),
            "GR" => array("GREECE", "GR", "GRC", "300"),
            "GL" => array("GREENLAND", "GL", "GRL", "304"),
            "GD" => array("GRENADA", "GD", "GRD", "308"),
            "GP" => array("GUADELOUPE", "GP", "GLP", "312"),
            "GU" => array("GUAM", "GU", "GUM", "316"),
            "GT" => array("GUATEMALA", "GT", "GTM", "320"),
            "GN" => array("GUINEA", "GN", "GIN", "324"),
            "GW" => array("GUINEA-BISSAU", "GW", "GNB", "624"),
            "GY" => array("GUYANA", "GY", "GUY", "328"),
            "HT" => array("HAITI", "HT", "HTI", "332"),
            "HM" => array("HEARD ISLAND & MCDONALD ISLANDS", "HM", "HMD", "334"),
            "HN" => array("HONDURAS", "HN", "HND", "340"),
            "HK" => array("HONG KONG", "HK", "HKG", "344"),
            "HU" => array("HUNGARY", "HU", "HUN", "348"),
            "IS" => array("ICELAND", "IS", "ISL", "352"),
            "IN" => array("INDIA", "IN", "IND", "356"),
            "ID" => array("INDONESIA", "ID", "IDN", "360"),
            "IR" => array("IRAN, ISLAMIC REPUBLIC OF", "IR", "IRN", "364"),
            "IQ" => array("IRAQ", "IQ", "IRQ", "368"),
            "IE" => array("IRELAND", "IE", "IRL", "372"),
            "IL" => array("ISRAEL", "IL", "ISR", "376"),
            "IT" => array("ITALY", "IT", "ITA", "380"),
            "JM" => array("JAMAICA", "JM", "JAM", "388"),
            "JP" => array("JAPAN", "JP", "JPN", "392"),
            "JO" => array("JORDAN", "JO", "JOR", "400"),
            "KZ" => array("KAZAKHSTAN", "KZ", "KAZ", "398"),
            "KE" => array("KENYA", "KE", "KEN", "404"),
            "KI" => array("KIRIBATI", "KI", "KIR", "296"),
            "KP" => array("KOREA, DEMOCRATIC PEOPLE'S REPUBLIC OF", "KP", "PRK", "408"),
            "KR" => array("KOREA, REPUBLIC OF", "KR", "KOR", "410"),
            "KW" => array("KUWAIT", "KW", "KWT", "414"),
            "KG" => array("KYRGYZSTAN", "KG", "KGZ", "417"),
            "LA" => array("LAO PEOPLE'S DEMOCRATIC REPUBLIC", "LA", "LAO", "418"),
            "LV" => array("LATVIA", "LV", "LVA", "428"),
            "LB" => array("LEBANON", "LB", "LBN", "422"),
            "LS" => array("LESOTHO", "LS", "LSO", "426"),
            "LR" => array("LIBERIA", "LR", "LBR", "430"),
            "LY" => array("LIBYAN ARAB JAMAHIRIYA", "LY", "LBY", "434"),
            "LI" => array("LIECHTENSTEIN", "LI", "LIE", "438"),
            "LT" => array("LITHUANIA", "LT", "LTU", "440"),
            "LU" => array("LUXEMBOURG", "LU", "LUX", "442"),
            "MO" => array("MACAU", "MO", "MAC", "446"),
            "MK" => array("MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF", "MK", "MKD", "807"),
            "MG" => array("MADAGASCAR", "MG", "MDG", "450"),
            "MW" => array("MALAWI", "MW", "MWI", "454"),
            "MY" => array("MALAYSIA", "MY", "MYS", "458"),
            "MV" => array("MALDIVES", "MV", "MDV", "462"),
            "ML" => array("MALI", "ML", "MLI", "466"),
            "MT" => array("MALTA", "MT", "MLT", "470"),
            "MH" => array("MARSHALL ISLANDS", "MH", "MHL", "584"),
            "MQ" => array("MARTINIQUE", "MQ", "MTQ", "474"),
            "MR" => array("MAURITANIA", "MR", "MRT", "478"),
            "MU" => array("MAURITIUS", "MU", "MUS", "480"),
            "YT" => array("MAYOTTE", "YT", "MYT", "175"),
            "MX" => array("MEXICO", "MX", "MEX", "484"),
            "FM" => array("MICRONESIA, FEDERATED STATES OF", "FM", "FSM", "583"),
            "MD" => array("MOLDOVA, REPUBLIC OF", "MD", "MDA", "498"),
            "MC" => array("MONACO", "MC", "MCO", "492"),
            "MN" => array("MONGOLIA", "MN", "MNG", "496"),
            "MS" => array("MONTSERRAT", "MS", "MSR", "500"),
            "MA" => array("MOROCCO", "MA", "MAR", "504"),
            "MZ" => array("MOZAMBIQUE", "MZ", "MOZ", "508"),
            "MM" => array("MYANMAR", "MM", "MMR", "104"),
            "NA" => array("NAMIBIA", "NA", "NAM", "516"),
            "NR" => array("NAURU", "NR", "NRU", "520"),
            "NP" => array("NEPAL", "NP", "NPL", "524"),
            "NL" => array("NETHERLANDS", "NL", "NLD", "528"),
            "AN" => array("NETHERLANDS ANTILLES", "AN", "ANT", "530"),
            "NC" => array("NEW CALEDONIA", "NC", "NCL", "540"),
            "NZ" => array("NEW ZEALAND", "NZ", "NZL", "554"),
            "NI" => array("NICARAGUA", "NI", "NIC", "558"),
            "NE" => array("NIGER", "NE", "NER", "562"),
            "NG" => array("NIGERIA", "NG", "NGA", "566"),
            "NU" => array("NIUE", "NU", "NIU", "570"),
            "NF" => array("NORFOLK ISLAND", "NF", "NFK", "574"),
            "MP" => array("NORTHERN MARIANA ISLANDS", "MP", "MNP", "580"),
            "NO" => array("NORWAY", "NO", "NOR", "578"),
            "OM" => array("OMAN", "OM", "OMN", "512"),
            "PK" => array("PAKISTAN", "PK", "PAK", "586"),
            "PW" => array("PALAU", "PW", "PLW", "585"),
            "PA" => array("PANAMA", "PA", "PAN", "591"),
            "PG" => array("PAPUA NEW GUINEA", "PG", "PNG", "598"),
            "PY" => array("PARAGUAY", "PY", "PRY", "600"),
            "PE" => array("PERU", "PE", "PER", "604"),
            "PH" => array("PHILIPPINES", "PH", "PHL", "608"),
            "PN" => array("PITCAIRN", "PN", "PCN", "612"),
            "PL" => array("POLAND", "PL", "POL", "616"),
            "PT" => array("PORTUGAL", "PT", "PRT", "620"),
            "PR" => array("PUERTO RICO", "PR", "PRI", "630"),
            "QA" => array("QATAR", "QA", "QAT", "634"),
            "RE" => array("REUNION", "RE", "REU", "638"),
            "RO" => array("ROMANIA", "RO", "ROU", "642"),
            "RU" => array("RUSSIAN FEDERATION", "RU", "RUS", "643"),
            "RW" => array("RWANDA", "RW", "RWA", "646"),
            "KN" => array("SAINT KITTS AND NEVIS", "KN", "KNA", "659"),
            "LC" => array("SAINT LUCIA", "LC", "LCA", "662"),
            "VC" => array("SAINT VINCENT AND THE GRENADINES", "VC", "VCT", "670"),
            "WS" => array("SAMOA", "WS", "WSM", "882"),
            "SM" => array("SAN MARINO", "SM", "SMR", "674"),
            "ST" => array("SAO TOME AND PRINCIPE", "ST", "STP", "678"),
            "SA" => array("SAUDI ARABIA", "SA", "SAU", "682"),
            "SN" => array("SENEGAL", "SN", "SEN", "686"),
            "RS" => array("SERBIA", "RS", "SRB", "688"),
            "SC" => array("SEYCHELLES", "SC", "SYC", "690"),
            "SL" => array("SIERRA LEONE", "SL", "SLE", "694"),
            "SG" => array("SINGAPORE", "SG", "SGP", "702"),
            "SK" => array("SLOVAKIA (Slovak Republic)", "SK", "SVK", "703"),
            "SI" => array("SLOVENIA", "SI", "SVN", "705"),
            "SB" => array("SOLOMON ISLANDS", "SB", "SLB", "90"),
            "SO" => array("SOMALIA", "SO", "SOM", "706"),
            "ZA" => array("SOUTH AFRICA", "ZA", "ZAF", "710"),
            "ES" => array("SPAIN", "ES", "ESP", "724"),
            "LK" => array("SRI LANKA", "LK", "LKA", "144"),
            "SH" => array("SAINT HELENA", "SH", "SHN", "654"),
            "PM" => array("SAINT PIERRE AND MIQUELON", "PM", "SPM", "666"),
            "SD" => array("SUDAN", "SD", "SDN", "736"),
            "SR" => array("SURINAME", "SR", "SUR", "740"),
            "SJ" => array("SVALBARD AND JAN MAYEN ISLANDS", "SJ", "SJM", "744"),
            "SZ" => array("SWAZILAND", "SZ", "SWZ", "748"),
            "SE" => array("SWEDEN", "SE", "SWE", "752"),
            "CH" => array("SWITZERLAND", "CH", "CHE", "756"),
            "SY" => array("SYRIAN ARAB REPUBLIC", "SY", "SYR", "760"),
            "TW" => array("TAIWAN, PROVINCE OF CHINA", "TW", "TWN", "158"),
            "TJ" => array("TAJIKISTAN", "TJ", "TJK", "762"),
            "TZ" => array("TANZANIA, UNITED REPUBLIC OF", "TZ", "TZA", "834"),
            "TH" => array("THAILAND", "TH", "THA", "764"),
            "TG" => array("TOGO", "TG", "TGO", "768"),
            "TK" => array("TOKELAU", "TK", "TKL", "772"),
            "TO" => array("TONGA", "TO", "TON", "776"),
            "TT" => array("TRINIDAD AND TOBAGO", "TT", "TTO", "780"),
            "TN" => array("TUNISIA", "TN", "TUN", "788"),
            "TR" => array("TURKEY", "TR", "TUR", "792"),
            "TM" => array("TURKMENISTAN", "TM", "TKM", "795"),
            "TC" => array("TURKS AND CAICOS ISLANDS", "TC", "TCA", "796"),
            "TV" => array("TUVALU", "TV", "TUV", "798"),
            "UG" => array("UGANDA", "UG", "UGA", "800"),
            "UA" => array("UKRAINE", "UA", "UKR", "804"),
            "AE" => array("UNITED ARAB EMIRATES", "AE", "ARE", "784"),
            "GB" => array("UNITED KINGDOM", "GB", "GBR", "826"),
            "US" => array("UNITED STATES", "US", "USA", "840"),
            "UM" => array("UNITED STATES MINOR OUTLYING ISLANDS", "UM", "UMI", "581"),
            "UY" => array("URUGUAY", "UY", "URY", "858"),
            "UZ" => array("UZBEKISTAN", "UZ", "UZB", "860"),
            "VU" => array("VANUATU", "VU", "VUT", "548"),
            "VA" => array("VATICAN CITY STATE (HOLY SEE)", "VA", "VAT", "336"),
            "VE" => array("VENEZUELA", "VE", "VEN", "862"),
            "VN" => array("VIET NAM", "VN", "VNM", "704"),
            "VG" => array("VIRGIN ISLANDS (BRITISH)", "VG", "VGB", "92"),
            "VI" => array("VIRGIN ISLANDS (U.S.)", "VI", "VIR", "850"),
            "WF" => array("WALLIS AND FUTUNA ISLANDS", "WF", "WLF", "876"),
            "EH" => array("WESTERN SAHARA", "EH", "ESH", "732"),
            "YE" => array("YEMEN", "YE", "YEM", "887"),
            "YU" => array("YUGOSLAVIA", "YU", "YUG", "891"),
            "ZR" => array("ZAIRE", "ZR", "ZAR", "180"),
            "ZM" => array("ZAMBIA", "ZM", "ZMB", "894"),
            "ZW" => array("ZIMBABWE", "ZW", "ZWE", "716"),
        );

        return $countries[$two_digit_code];

    }


    /**
     * Determine method availability based on quote amount and config data
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        /*if ($quote && (
            $quote->getBaseGrandTotal() < $this->_minAmount
            || ($this->_maxAmount && $quote->getBaseGrandTotal() > $this->_maxAmount))
        ) {
            return false;
        }*/

        if (!$this->getConfigData('api_key') and !$this->getConfigData('api_key_test')) {
            return false;
        }

        return parent::isAvailable($quote);
    }

    /**
     * Availability for currency
     *
     * @param string $currencyCode
     * @return bool
     */
    /*public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->_supportedCurrencyCodes)) {
            return false;
        }
        return true;
    }*/
}
