<?php 
namespace ClassWallet\Payment\Model;

use \ClassWallet\Payment\Api\OrderInterface;
use Magento\Sales\Model\Order;
use ClassWallet\Payment\Logger\Logger;

class OrderManagement implements OrderInterface
{

    CONST USENAME_PATH    =   'payment/classwallet/api_username';
    CONST PASSWORD_PATH   =   'payment/classwallet/api_password';

  	/**
   	* @var \Magento\Framework\App\RequestInterface
   	*/
  	protected $request;

    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $_invoiceService; 

    private $logger;   

  	/**
   	* @param \Magento\Framework\App\RequestInterface $request
   	*/
  	public function __construct(
      	\Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository,
        Logger $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\Serializer\Json $json
  	)
  	{
      	$this->request            =   $request;
        $this->storeManager       =   $storeManager;
        $this->orderRepository    =   $orderRepository;
        $this->orderFactory       =   $orderFactory;
        $this->_invoiceService    =   $invoiceService;
        $this->_transaction       =   $transaction;
        $this->invoiceSender      =   $invoiceSender;
        $this->transactionRepository = $transactionRepository;
        $this->logger             =   $logger;
        $this->scopeConfig        =   $scopeConfig;
        $this->json               =   $json;
  	}

  	public function getOrder($orderId) {
		$this->logger->info("Getting info for order id $orderId");

      $returnData             =   array();
        
      try{
		  $this->logger->info('Authenticating...');
          $auth   = $this->authenticate();

          if(!$auth){
			  $this->logger->info('Authentication failed');
              throw new \Exception("HTTP/1.1 401 Authorization Required");
          }

          $orderData = $this->orderRepository->get($orderId);

          $orderItems             =   array();

          foreach ($orderData->getAllItems() as $item) {
              $orderItems[]   =   array(
                                      'itemId'        => $item->getItemId(),
                                      'description'   => $item->getName(),  
                                      'quantity'      => (int)$item->getQtyOrdered(),  
                                      'price'         => number_format($item->getPrice(), 2, '.', '')
                                  );
          }

          $returnData                =  array(
                                                'orderId' =>  $orderData->getEntityId(),
                                                'details' =>  array(
                                                                'items' => $orderItems,
                                                                'shipping' => number_format($orderData->getShippingAmount(), 2, '.', ''),
                                                                'tax' => number_format($orderData->getTaxAmount(), 2, '.', '')
                                                              )
                                          );                       
      }catch(\Exception $e){
        $returnData['status']   =   'error';
        $returnData['message']  =   $e->getMessage();
		$this->logger->info('Caught exception ' . $e->getMessage());
      }  

      $jsonResponse      =   $this->json->serialize($returnData);
	  $this->logger->info('Order data ' . $jsonResponse);
      echo($jsonResponse);
      exit;

  	}

    public function setClasswalletOrder($orderId) {
        
        try{

          $data         =   $this->request->getContent();
          $postData     =   json_decode($data, true);
          $message      =   '';

          $auth   = $this->authenticate();

          if(!$auth){
              throw new \Exception("HTTP/1.1 401 Authorization Required");
          }
          
          $purchaseOrderId  =   $postData['PurchaseOrderId'];
          $invoiceResult    =   $this->markOrderComplete($orderId, $purchaseOrderId);

          if($invoiceResult['status']){
              $returnData   =   array('status' =>  'complete');    
          }else{
              $returnData   =   array('status' =>  'failed', 'message' => $invoiceResult['message']);    
          }

        }catch(\Exception $e){
          $returnData             =   array();
          $returnData['status']   =   'failed';
          $returnData['message']  =   $e->getMessage();

        }          

        $jsonResonse      =   $this->json->serialize($returnData);
        echo($jsonResonse);
        exit; 

    }  

    protected function markOrderComplete($orderId, $purchaseOrderId){
        $orderData = $this->orderRepository->get($orderId);

        try{

            if(!$orderData->canInvoice()) {
              throw new \Exception("Unable to create invoices.");
            }

            $payment = $orderData->getPayment();
            $payment->setTransactionId($purchaseOrderId);
                
            $orderData->setState(Order::STATE_PROCESSING)->setStatus($orderData->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING));
              
            $orderData->setTotalPaid($orderData->getGrandTotal());
            $orderData->setBaseTotalPaid($orderData->getBaseGrandTotal());

            $transaction = $this->transactionRepository->getByTransactionId(
                "-1",
                $payment->getId(),
                $orderData->getId()
            );

            if($transaction) {
              $transaction->setTxnId($purchaseOrderId);
              $transaction->setAdditionalInformation(  
                "ClassWallet Transaction Id",$purchaseOrderId
              );
              $transaction->setAdditionalInformation(  
                "status","successful"
              );
              $transaction->setIsClosed(1);
              $transaction->save(); 
            }
              //exit;
              
            $payment->addTransactionCommentsToOrder(
              $transaction,
               "Transaction is completed successfully"
            );
            $payment->setParentTransactionId(null); 
            $additionalInfo   = array(
                                   "method_title" => "Class Wallet",
                                   "purchase_order_id" => $purchaseOrderId
                                );

            $payment->setAdditionalInformation(
                [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => $additionalInfo]
            );  
              
            $payment->save();
            $orderData->save();
              
            $invoice = $this->_invoiceService->prepareInvoice($orderData);
            $invoice->register();
            $invoice->setState(\Magento\Sales\Model\Order\Invoice::STATE_PAID);
            $transactionSave = $this->_transaction->addObject($invoice)->addObject($orderData);
            $transactionSave->save();
            $invoice->save();

            $orderData->setState(\Magento\Sales\Model\Order::STATE_COMPLETE);
            $orderData->setStatus(\Magento\Sales\Model\Order::STATE_COMPLETE);
            $orderData->addStatusToHistory($orderData->getStatus(), 'Order completed successfully');
            $orderData->save();

            return array("status" => true);

        }catch(\Exception $e){
            return array("status" => false, "message" => $e->getMessage());
        }

    }  

    private function authenticate(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $apiUser    = $this->scopeConfig->getValue(self::USENAME_PATH, $storeScope);
        $apiPass    = $this->scopeConfig->getValue(self::PASSWORD_PATH, $storeScope);

        $u = '';
        $p = '';

        if(!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            preg_match('/^Basic\s+(.*)$/i', $_SERVER['HTTP_AUTHORIZATION'], $user_pass);
            $str          =   base64_decode($user_pass[1]);
            list($u, $p ) =   explode(':', $str);
        } else if(!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
            $u = $_SERVER['PHP_AUTH_USER'];
            $p = $_SERVER['PHP_AUTH_PW'];
        }

        if($u != $apiUser || $p != $apiPass) {
          return false;
        }else{
          return true;
        }
    }
}
