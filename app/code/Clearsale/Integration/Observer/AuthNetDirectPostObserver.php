<?php

namespace Clearsale\Integration\Observer;

use Magento\Framework\Event\Observer;
use \Clearsale\Integration\Observer\ClearsaleObserver;

class DirectPostObserver extends ClearsaleObserver{
    public function execute(Observer $observer, $ignoreDPM = true){
        $this->logger->info('ClearSale:Starting directpostObserver');

         $request = $observer->getEvent()->getRequest();
         $reponse = $observer->getEvent()->getResponse();
         
         $this->logger->info('ClearSale:' . $reponse->getXResponseCode());

         $orderIncrementId = $request->getParam('x_invoice_num');
 
         if (!empty($orderIncrementId)) {
             $order = $this->objectManagerInterface->create('\Magento\Sales\Model\Order');
             $order->loadByIncrementId($orderIncrementId);
 
             if ($order instanceof \Magento\Sales\Model\Order) {
                 $observer->getEvent()->setOrder($order);
             }
         }
 
         return parent::execute($observer, false);
    }
}