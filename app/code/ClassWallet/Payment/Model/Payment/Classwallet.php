<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ClassWallet\Payment\Model\Payment;


class Classwallet extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_isInitializeNeeded      =   false;
    protected $redirect_uri;
    protected $_code                    =   'classwallet';
    protected $_canOrder                =   true;
    protected $_isGateway               =   true; 
    
    public function getOrderPlaceRedirectUrl() {
       return \Magento\Framework\App\ObjectManager::getInstance()
                            ->get('Magento\Framework\UrlInterface')->getUrl("classwallet/redirect");
   } 
}
