<?php
namespace ClassWallet\Payment\Model\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
# added this observer if javascript don't redirect to instamojo/redirect url.
class ControllerActionPredispatch implements ObserverInterface {
	private $us_state_table = array(
		'AL'=>'ALABAMA',
		'AK'=>'ALASKA',
		'AS'=>'AMERICAN SAMOA',
		'AZ'=>'ARIZONA',
		'AR'=>'ARKANSAS',
		'CA'=>'CALIFORNIA',
		'CO'=>'COLORADO',
		'CT'=>'CONNECTICUT',
		'DE'=>'DELAWARE',
		'DC'=>'DISTRICT OF COLUMBIA',
		'FM'=>'FEDERATED STATES OF MICRONESIA',
		'FL'=>'FLORIDA',
		'GA'=>'GEORGIA',
		'GU'=>'GUAM GU',
		'HI'=>'HAWAII',
		'ID'=>'IDAHO',
		'IL'=>'ILLINOIS',
		'IN'=>'INDIANA',
		'IA'=>'IOWA',
		'KS'=>'KANSAS',
		'KY'=>'KENTUCKY',
		'LA'=>'LOUISIANA',
		'ME'=>'MAINE',
		'MH'=>'MARSHALL ISLANDS',
		'MD'=>'MARYLAND',
		'MA'=>'MASSACHUSETTS',
		'MI'=>'MICHIGAN',
		'MN'=>'MINNESOTA',
		'MS'=>'MISSISSIPPI',
		'MO'=>'MISSOURI',
		'MT'=>'MONTANA',
		'NE'=>'NEBRASKA',
		'NV'=>'NEVADA',
		'NH'=>'NEW HAMPSHIRE',
		'NJ'=>'NEW JERSEY',
		'NM'=>'NEW MEXICO',
		'NY'=>'NEW YORK',
		'NC'=>'NORTH CAROLINA',
		'ND'=>'NORTH DAKOTA',
		'MP'=>'NORTHERN MARIANA ISLANDS',
		'OH'=>'OHIO',
		'OK'=>'OKLAHOMA',
		'OR'=>'OREGON',
		'PW'=>'PALAU',
		'PA'=>'PENNSYLVANIA',
		'PR'=>'PUERTO RICO',
		'RI'=>'RHODE ISLAND',
		'SC'=>'SOUTH CAROLINA',
		'SD'=>'SOUTH DAKOTA',
		'TN'=>'TENNESSEE',
		'TX'=>'TEXAS',
		'UT'=>'UTAH',
		'VT'=>'VERMONT',
		'VI'=>'VIRGIN ISLANDS',
		'VA'=>'VIRGINIA',
		'WA'=>'WASHINGTON',
		'WV'=>'WEST VIRGINIA',
		'WI'=>'WISCONSIN',
		'WY'=>'WYOMING',
		'AE'=>'ARMED FORCES AFRICA \ CANADA \ EUROPE \ MIDDLE EAST',
		'AA'=>'ARMED FORCES AMERICA (EXCEPT CANADA)',
		'AP'=>'ARMED FORCES PACIFIC'
	);
	protected $checkoutSession;
	protected $orderFactory;
	public function __construct (
		Session $checkoutSession,
		OrderFactory $orderFactory,
		\Magento\Catalog\Model\Session $catalogSession,
    	\Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionColl
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory; 
	  	$this->catalogSession = $catalogSession;
      	$this->regionColl     =   $regionColl;
    }

	public function execute(\Magento\Framework\Event\Observer $observer) {
		$request =$observer->getData('request'); 
		if($request->getModuleName() == "checkout" and $request->getActionName()== "success"){
			$orderId = $this->checkoutSession->getLastOrderId();
			if($orderId){
				$order = $this->orderFactory->create()->load($orderId);
				if($order->getPayment()->getMethodInstance()->getCode()== "classwallet" and $order->getState()== Order::STATE_NEW   )
				{
					// Override addresses
					$this->updateAddresses($order);

					$this->urlBuilder = \Magento\Framework\App\ObjectManager::getInstance()
							->get('Magento\Framework\UrlInterface');
					$url = $this->urlBuilder->getUrl("classwallet/redirect");
					header("Location:$url");
					exit;
				}
			}
		}		
	}

	/**
	 * Overwrites with ClassWallet's billing address and the shipping address sent from CW
	 */
	private function updateAddresses($order) {
		$cw_address_raw =  $this->catalogSession->getCWAddress();
		$cw_address = json_decode($cw_address_raw['request'], true);
		$name_arr = explode(' ', $cw_address['username']);
		$first_name = $name_arr[0];
		$last_name = $name_arr[1];
		$shipping_data = $cw_address['shipping'];
		$full_state = ucfirst(strtolower($this->us_state_table[$shipping_data['state']]));

		// Shipping
		$order->getShippingAddress()->setFirstname($first_name);
		$order->getShippingAddress()->setLastname($last_name);
		$order->getShippingAddress()->setStreet($shipping_data['address']);
		$order->getShippingAddress()->setCity($shipping_data['city']);
		$order->getShippingAddress()->setRegion($full_state);
		//$order->getShippingAddress()->setRegionId($this->getRegionId($full_state));
		$order->getShippingAddress()->setCountryId('US');
		$order->getShippingAddress()->setTelephone('XXX-XXX-XXXX');

		// Billing
		$order->getBillingAddress()->setFirstname('Class');
		$order->getBillingAddress()->setLastname('Wallet');
		$order->getBillingAddress()->setStreet("6100 Holywood Blvd \nSuite 409");
		$order->getBillingAddress()->setCity('Hollywood');
		$order->getBillingAddress()->setTelephone('877-969-5536');
		$order->getBillingAddress()->setRegion('Florida');
		//$order->getBillingAddress()->setRegionId('Florida');
		$order->getBillingAddress()->setCountryId('US');

		$order->save();
	}

	private  function getRegionId($regionCode){
      try{
          $region   =   $this->regionColl->create()->addFieldToFilter(['code', 'default_name'],
                        [
                            ['eq' => $regionCode],
                            ['eq' => $regionCode]
                        ])->getFirstItem();


          return $region;
      }catch(\Exception $e){
          
      }    
  }

}
