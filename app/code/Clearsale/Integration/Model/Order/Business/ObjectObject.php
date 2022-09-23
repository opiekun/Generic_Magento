<?php
namespace Clearsale\Integration\Model\Order\Business;

use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ObjectObject
{
	public $statusHandle;
	public $Http;

    protected $salesOrderFactory;
    protected $resourceConnection;
    protected $logger;
    protected $orderRepository;
	protected $orderPaymentInterface;
    protected $totalUtilsHttpHelper;
    protected $orderInterface;

    function __construct(
        \Magento\Sales\Model\OrderFactory $salesOrderFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        LoggerInterface $logger,
				ScopeConfigInterface $scopeConfig,
        \Clearsale\Integration\Model\Utils\Status $statusHandle,
        \Clearsale\Integration\Model\Utils\HttpHelperFactory $totalUtilsHttpHelper,
				\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
				\Magento\Sales\Api\Data\OrderPaymentInterface $orderPaymentInterface,
				\Magento\Sales\Api\Data\OrderInterface $orderInterface

    ) {
        $this->statusHandle = $statusHandle;
        $this->orderPaymentInterface = $orderPaymentInterface;
        $this->totalUtilsHttpHelper = $totalUtilsHttpHelper;
        $this->orderInterface = $orderInterface;
        $this->salesOrderFactory = $salesOrderFactory;
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
				$this->clearSaleTotalConfig = $scopeConfig;
				$this->orderRepository = $orderRepository;
				//$this->statusHandle = $this->totalUtilsStatusFactory->create();
				$this->Http = $this->totalUtilsHttpHelper->create();
	}
	
	public function send($requestSend,$environment) {

		$url = $environment."api/order/send/";
		$response = $this->Http->postData($requestSend, $url);	
		
		return $response;
	}
	
	public function get($requestGet,$environment)
	{
		$url = $environment."api/order/get/";
		$response = $this->Http->postData($requestGet, $url);	
		return $response;
	}
	
	public function saveWithoutStore($order)
	{			
	 $magentoStatus = $this->statusHandle->toMagentoStatus($order->Status);
	 if($magentoStatus)
	  {
			if($magentoStatus != "")
			{
				$this->setOrderStatus($order,$magentoStatus);
			}
	  }
	}

	public function save($order, $storeId)
	{			
	 $magentoStatus = $this->statusHandle->toMagentoStatus($order->Status);
	 if($magentoStatus)
	  {
			if($magentoStatus != "")
			{
				$magentoStatus = $this->MagentoStatusConversion($magentoStatus, $storeId);
				$this->setOrderStatus($order,$magentoStatus);
			}
	  }
	}
	
	public function update($orderStatus, $storeId)
	{ 
	  $magentoStatus = $this->statusHandle->toMagentoStatus($orderStatus->Status);
	  if($magentoStatus && $magentoStatus != "")
	  {  	
			$magentoStatus = $this->MagentoStatusConversion($magentoStatus, $storeId);
			$this->setOrderStatus($orderStatus,$magentoStatus);
		}
	}

	private function MagentoStatusConversion($magentoStatus, $storeId)
	{
			$magentoStatusConverted = $magentoStatus;

			if($magentoStatus == 'analyzing_clearsale')
			{
				$magentoStatusConverted = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/analyzing_clearsale',\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
			}
			else if($magentoStatus == 'approved_clearsale')
			{
				$magentoStatusConverted = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/approved_clearsale',\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
			}
			else if($magentoStatus == 'denied_clearsale')
			{
				$magentoStatusConverted = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/denied_clearsale',\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
			}
			else if($magentoStatus == 'canceled_clearsale')
			{
				$magentoStatusConverted = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/canceled_clearsale',\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
			}

			return $magentoStatusConverted;
	}
	
	public function setOrderStatus($orderStat,$status)
	{	
		$order = $orderStat->order; //$this->orderInterface->load($orderStat->getId());
		if($order->getStatus() != $status)
		{
			$order->setStatus($status);	
			$order->addStatusToHistory($status, 'Clearsale Status Update', false);
			
			
			$order->save();
		}		
	}
	
		
	private function insertClearsaleOrderControl($orderId,$message)
	{	
		try{
			$orderArray["order_id"] = $orderId;
			$orderArray["diagnostics"] = $message;
			$orderArray["attempts"] = 1;
			$orderArray["dt_update"] = date('Y-m-d H:i:s');
                        $orderArray["dt_sent"] = '';
			
			$connection = $this->resourceConnection->getconnection('core_write');	
			$connection->insert('clearsale_order_control', $orderArray);					 						
		} 
		catch (\Exception $e)
		{	
			$this->logger->info($e->getMessage());			
		}
	}	
	
	
	private function updateClearsaleOrderControl($orderId,$message,$attempts,$sent)
	{
		try {  
			$orderArray["order_id"] = $orderId;
			$orderArray["diagnostics"] = $message;
			$orderArray["attempts"] = $attempts;			
		 	$orderArray["dt_update"] = date('Y-m-d H:i:s');
                        
			print_r("Tentativas $attempts");
			
			if($sent)
			{
			 $orderArray["dt_sent"] = date('Y-m-d H:i:s');	
			}
			
			$connection = $this->resourceConnection->getconnection('core_write'); 
			$__where = $connection->quoteInto('order_id = ?', $orderArray["order_id"]);
			$connection->update('clearsale_order_control', $orderArray, $__where);	
			print_r($__where);
			
		} catch (\Exception $e){  
			print_r("Error: ".$e->getMessage());
						
			$this->logger->info($e->getMessage());	 
		}  
		
	}
	
	
	private function selectClearsaleOrderControl($maxAttemps)
	{
		try {  
			$connection = $this->resourceConnection->getconnection('core_read'); 
			$query = "SELECT * FROM `clearsale_order_control` WHERE `dt_sent` = '0000-00-00 00:00:00' AND `attempts` <=".$maxAttemps;
			$results = $connection->fetchAll($query);
		
			return $results;
                        			
		} catch (\Exception $e){  
						
			$this->logger->info($e->getMessage());	 
			print_r($e->getMessage());
		}  
		
	}
	
			

	
	public function createOrderControl($orderid,$message)
	{	
	  $this->insertClearsaleOrderControl($orderid,$message);		
	}
	
	public function setOrderControl($orderId,$sent,$attemps,$message)
	{	
	   $this->updateClearsaleOrderControl($orderId,$message,$attemps,$sent);
	}
	
	public function getOrderControl()
	{
            $maxAttemps = 7;
	   return $this->selectClearsaleOrderControl($maxAttemps);
	}
	
	
	public function objectOrderToArray($order)
	{
		$array_order["order_id"] = $order->ID;
		$array_order["clearsale_status"] = $order->Status;
		$array_order["score"] = $order->Score;	
		return $array_order;
	}

}



