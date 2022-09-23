<?php
namespace ClassWallet\Payment\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\OrderFactory;
use ClassWallet\Payment\Logger\Logger;
use Magento\Sales\Model\Order\Payment\Transaction\Builder as TransactionBuilder;

 
class Main extends  \Magento\Framework\View\Element\Template
{

	 protected $checkoutSession;
	 protected $orderFactory;
	 protected $urlBuilder;
	 private $logger;
	 protected $config;
	 protected $messageManager;
	 protected $transactionBuilder;

	 public function __construct(Context $context,
			Session $checkoutSession,
			OrderFactory $orderFactory,
			Logger $logger,
			TransactionBuilder $tb
		) {
      
        $this->checkoutSession 	= 	$checkoutSession;
        $this->orderFactory 	= 	$orderFactory;
        $this->config 			= 	$context->getScopeConfig();
        $this->transactionBuilder = $tb;
		$this->logger 			= 	$logger;				
		$this->urlBuilder 		= 	\Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
		parent::__construct($context);
    }

	protected function _prepareLayout()
	{
		$orderId 	= 	$this->checkoutSession->getLastOrderId();
		$this->logger->info("Creating Order for orderId $orderId");
		$order 		= 	$this->orderFactory->create()->load($orderId);
		if ($order){

			$payment 	= 	$order->getPayment();
			$payment->setTransactionId("-1");
			$payment->setAdditionalInformation(  
				[\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => array("Transaction is yet to complete")]
			);

			$trn = $payment->addTransaction(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE,null,true);
			$trn->setIsClosed(0)->save();

			$payment->addTransactionCommentsToOrder(
                $trn,
               "The transaction is yet to complete."
            );

            $payment->setParentTransactionId(null);
            $payment->save();
            $order->save();
			
		} else {
			$this->logger->info('Order with ID $orderId not found. Quitting :-(');
		}
	}

	public function redirectUrl(){
		$orderId 		= 	$this->checkoutSession->getLastOrderId();
		$url 			=	$this->urlBuilder->getBaseUrl();
		$storeScope 	= 	\Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		$vendorId 		= 	$this->config->getValue("payment/classwallet/vendor_id",$storeScope);
		$endPointCW 	= 	'https://app.classwallet.com/payby-checkout/';
		$enUrl 			=	"callback=" . urlencode("$url/rest/default/V1/invoice_process/$orderId") . "&vendorId={$vendorId}";
		$action 		=	"$endPointCW?{$enUrl}";
		return $action;
	}

}
